<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CashDrawerController extends Controller
{
    /**
     * Display the cash drawer / POS page with products for selection.
     */
    public function index()
    {
        $products = Product::with(['unit', 'productPrices'])->where('is_active', true)->orderBy('name')->get();
        $productsJson = $products->map(function ($p) {
            $prices = [
                ['label' => 'Selling price', 'price' => (float) $p->selling_price],
            ];
            foreach ($p->productPrices as $pp) {
                $prices[] = ['label' => $pp->label, 'price' => (float) $pp->price];
            }
            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code ?? '',
                'barcode' => $p->barcode ?? '',
                'price' => (float) $p->selling_price,
                'prices' => $prices,
                'unit' => $p->unit ? $p->unit->short_code : '',
            ];
        });
        $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $storeName = optional(auth()->user()->tenant)->name ?? config('app.name');
        return view('cash-drawer.index', compact('productsJson', 'invoiceNo', 'storeName'));
    }

    /**
     * Open the cash drawer.
     */
    public function open(Request $request)
    {
        // Logic to open cash drawer
        return response()->json(['success' => true, 'message' => 'Cash drawer opened']);
    }

    /**
     * Close the cash drawer.
     */
    public function close(Request $request)
    {
        // Logic to close cash drawer
        return response()->json(['success' => true, 'message' => 'Cash drawer closed']);
    }

    /**
     * Get cash drawer status.
     */
    public function status()
    {
        // Logic to get cash drawer status
        return response()->json(['status' => 'open']);
    }
}
