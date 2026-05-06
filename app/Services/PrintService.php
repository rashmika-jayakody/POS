<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class PrintService
{
    protected $businessSettings;

    protected $escposAvailable = false;

    public function __construct()
    {
        $user = auth()->user();
        $this->businessSettings = $user?->tenant?->businessSetting;
        $this->escposAvailable = class_exists(\Mike42\Escpos\Printer::class);
    }

    public function printReceipt(array $data, ?string $printerIp = null, ?string $printerName = null): bool
    {
        if (! $this->escposAvailable) {
            Log::warning('ESC/POS library not installed. Run: composer require mike42/escpos-php');

            return $this->printToBrowser($data);
        }

        try {
            $connector = $this->getConnector($printerIp, $printerName);

            if (! $connector) {
                Log::warning('No printer connector available');

                return $this->printToBrowser($data);
            }

            $printer = new \Mike42\Escpos\Printer($connector);

            try {
                $this->printHeader($printer, $data);
                $this->printItems($printer, $data);
                $this->printTotals($printer, $data);
                $this->printFooter($printer, $data);

                $printer->cut();
                $printer->close();

                return true;
            } catch (Exception $e) {
                $printer->close();
                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Print error: '.$e->getMessage());

            return false;
        }
    }

    public function printKitchenTicket(array $data, ?string $printerIp = null, ?string $printerName = null): bool
    {
        if (! $this->escposAvailable) {
            Log::warning('ESC/POS library not installed');

            return false;
        }

        try {
            $connector = $this->getConnector($printerIp, $printerName);

            if (! $connector) {
                return false;
            }

            $printer = new \Mike42\Escpos\Printer($connector);

            try {
                $printer->setEmphasis(true);
                $printer->setTextSize(2, 2);
                $printer->text("KITCHEN ORDER\n");
                $printer->setEmphasis(false);
                $printer->setTextSize(1, 1);

                $printer->text("Order: {$data['order_no']}\n");

                if (! empty($data['table'])) {
                    $printer->text("Table: {$data['table']}\n");
                }

                if (! empty($data['waiter'])) {
                    $printer->text("Waiter: {$data['waiter']}\n");
                }

                $printer->text(str_repeat('-', 32)."\n");

                foreach ($data['items'] as $item) {
                    $printer->setEmphasis(true);
                    $printer->text("{$item['qty']}x {$item['name']}\n");
                    $printer->setEmphasis(false);

                    if (! empty($item['special_instructions'])) {
                        $printer->text("  * {$item['special_instructions']}\n");
                    }

                    if (! empty($item['modifiers'])) {
                        foreach ($item['modifiers'] as $modifier) {
                            $printer->text("  + {$modifier['name']}\n");
                        }
                    }
                }

                $printer->text(str_repeat('-', 32)."\n");
                $printer->text('Time: '.now()->format('H:i:s')."\n");

                $printer->cut();
                $printer->close();

                return true;
            } catch (Exception $e) {
                $printer->close();
                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Kitchen print error: '.$e->getMessage());

            return false;
        }
    }

    protected function getConnector(?string $printerIp, ?string $printerName)
    {
        if (! $this->escposAvailable) {
            return null;
        }

        if ($printerIp) {
            return new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector($printerIp, 9100);
        }

        if ($printerName && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($printerName);
        }

        if ($printerName) {
            return new \Mike42\Escpos\PrintConnectors\FilePrintConnector($printerName);
        }

        return null;
    }

    protected function printHeader($printer, array $data): void
    {
        $storeName = $this->businessSettings?->business_name ?? config('app.name');
        $address = $this->businessSettings?->address ?? '';
        $phone = $this->businessSettings?->phone ?? '';

        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->text("{$storeName}\n");
        $printer->setEmphasis(false);
        $printer->setTextSize(1, 1);

        if ($address) {
            $printer->text("{$address}\n");
        }
        if ($phone) {
            $printer->text("Tel: {$phone}\n");
        }

        $printer->text(str_repeat('-', 32)."\n");
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);

        $invoiceNo = $data['invoice_no'] ?? $data['order_no'] ?? 'N/A';
        $printer->text("Invoice: {$invoiceNo}\n");
        $printer->text('Date: '.now()->format('Y-m-d H:i:s')."\n");

        $cashierName = auth()->user()->name ?? 'System';
        $printer->text("Cashier: {$cashierName}\n");

        if (! empty($data['customer_name'])) {
            $printer->text("Customer: {$data['customer_name']}\n");
        }

        $printer->text(str_repeat('-', 32)."\n");
    }

    protected function printItems($printer, array $data): void
    {
        $currencySymbol = $this->businessSettings?->currency_symbol ?? 'Rs';

        foreach ($data['items'] as $item) {
            $name = $item['name'];
            $qty = $item['qty'];
            $price = number_format($item['unit_price'], 2);
            $total = number_format($item['qty'] * $item['unit_price'], 2);

            $printer->text("{$name}\n");
            $printer->text("  {$qty} x {$currencySymbol}{$price}");
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_RIGHT);
            $printer->text("{$currencySymbol}{$total}\n");
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
        }

        $printer->text(str_repeat('-', 32)."\n");
    }

    protected function printTotals($printer, array $data): void
    {
        $currencySymbol = $this->businessSettings?->currency_symbol ?? 'Rs';
        $taxLabel = $this->businessSettings?->tax_label ?? 'Tax';

        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_RIGHT);

        $subtotal = $data['subtotal'] ?? 0;
        $printer->text("Subtotal: {$currencySymbol}".number_format($subtotal, 2)."\n");

        if (! empty($data['discount_total']) && $data['discount_total'] > 0) {
            $printer->text("Discount: -{$currencySymbol}".number_format($data['discount_total'], 2)."\n");
        }

        if (! empty($data['tax_total']) && $data['tax_total'] > 0) {
            $printer->text("{$taxLabel}: {$currencySymbol}".number_format($data['tax_total'], 2)."\n");
        }

        if (! empty($data['service_charge']) && $data['service_charge'] > 0) {
            $printer->text("Service: {$currencySymbol}".number_format($data['service_charge'], 2)."\n");
        }

        if (! empty($data['tip_amount']) && $data['tip_amount'] > 0) {
            $printer->text("Tip: {$currencySymbol}".number_format($data['tip_amount'], 2)."\n");
        }

        $printer->setEmphasis(true);
        $printer->setTextSize(2, 1);
        $grandTotal = $data['grand_total'] ?? 0;
        $printer->text("TOTAL: {$currencySymbol}".number_format($grandTotal, 2)."\n");
        $printer->setEmphasis(false);
        $printer->setTextSize(1, 1);

        if (! empty($data['payment_method'])) {
            $printer->text('Payment: '.ucfirst($data['payment_method'])."\n");
        }

        $printer->text(str_repeat('-', 32)."\n");
    }

    protected function printFooter($printer, array $data): void
    {
        $footer = $this->businessSettings?->receipt_footer ?? 'Thank you for your business!';

        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
        $printer->text("\n{$footer}\n");
        $printer->text("\n\n");
    }

    protected function printToBrowser(array $data): bool
    {
        return true;
    }

    public function generateReceiptHtml(array $data): string
    {
        $currencySymbol = $this->businessSettings?->currency_symbol ?? 'Rs';
        $storeName = $this->businessSettings?->business_name ?? config('app.name');
        $address = $this->businessSettings?->address ?? '';
        $phone = $this->businessSettings?->phone ?? '';
        $footer = $this->businessSettings?->receipt_footer ?? 'Thank you for your business!';
        $taxLabel = $this->businessSettings?->tax_label ?? 'Tax';

        $invoiceNo = $data['invoice_no'] ?? $data['order_no'] ?? 'N/A';

        $html = '
        <div style="font-family: monospace; width: 280px; padding: 10px;">
            <div style="text-align: center; margin-bottom: 10px;">
                <strong style="font-size: 18px;">'.$storeName.'</strong><br>
                '.($address ? $address.'<br>' : '').'
                '.($phone ? 'Tel: '.$phone : '').'
            </div>
            <hr>
            <div>
                <strong>Invoice:</strong> '.$invoiceNo.'<br>
                <strong>Date:</strong> '.now()->format('Y-m-d H:i:s').'<br>
                <strong>Cashier:</strong> '.(auth()->user()->name ?? 'System').'<br>
                '.(! empty($data['customer_name']) ? '<strong>Customer:</strong> '.$data['customer_name'].'<br>' : '').'
            </div>
            <hr>
            <table style="width: 100%; border-collapse: collapse;">';

        foreach ($data['items'] as $item) {
            $total = $item['qty'] * $item['unit_price'];
            $html .= '
                <tr>
                    <td colspan="2">'.$item['name'].'</td>
                </tr>
                <tr>
                    <td>'.$item['qty'].' x '.$currencySymbol.number_format($item['unit_price'], 2).'</td>
                    <td style="text-align: right;">'.$currencySymbol.number_format($total, 2).'</td>
                </tr>';
        }

        $html .= '
            </table>
            <hr>
            <div style="text-align: right;">
                <div>Subtotal: '.$currencySymbol.number_format($data['subtotal'] ?? 0, 2).'</div>';

        if (! empty($data['discount_total']) && $data['discount_total'] > 0) {
            $html .= '<div>Discount: -'.$currencySymbol.number_format($data['discount_total'], 2).'</div>';
        }

        if (! empty($data['tax_total']) && $data['tax_total'] > 0) {
            $html .= '<div>'.$taxLabel.': '.$currencySymbol.number_format($data['tax_total'], 2).'</div>';
        }

        if (! empty($data['service_charge']) && $data['service_charge'] > 0) {
            $html .= '<div>Service: '.$currencySymbol.number_format($data['service_charge'], 2).'</div>';
        }

        $html .= '
                <div style="font-size: 16px; font-weight: bold; margin-top: 5px;">
                    TOTAL: '.$currencySymbol.number_format($data['grand_total'] ?? 0, 2).'
                </div>';

        if (! empty($data['payment_method'])) {
            $html .= '<div>Payment: '.ucfirst($data['payment_method']).'</div>';
        }

        $html .= '
            </div>
            <hr>
            <div style="text-align: center; margin-top: 10px;">
                '.$footer.'
            </div>
        </div>';

        return $html;
    }

    public function testPrinter(?string $printerIp = null, ?string $printerName = null): bool
    {
        if (! $this->escposAvailable) {
            return false;
        }

        try {
            $connector = $this->getConnector($printerIp, $printerName);

            if (! $connector) {
                return false;
            }

            $printer = new \Mike42\Escpos\Printer($connector);
            $printer->text("Printer Test\n");
            $printer->text('Time: '.now()->format('Y-m-d H:i:s')."\n");
            $printer->cut();
            $printer->close();

            return true;
        } catch (Exception $e) {
            Log::error('Printer test error: '.$e->getMessage());

            return false;
        }
    }
}
