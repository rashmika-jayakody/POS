<?php

namespace App\Console\Commands;

use App\Services\PayHereService;
use Illuminate\Console\Command;

class DebugPayhereHash extends Command
{
    protected $signature = 'payhere:debug {--plan=professional : Plan key}';

    protected $description = 'Debug PayHere checkout hash generation';

    public function handle()
    {
        $planKey = $this->option('plan');
        $plans = config('plans', []);
        $planConfig = $plans[$planKey] ?? null;

        if (! $planConfig) {
            $this->error("Plan '{$planKey}' not found. Available: " . implode(', ', array_keys($plans)));
            return 1;
        }

        $service = new PayHereService();
        $tenant = \App\Models\Tenant::first();

        if (! $tenant) {
            $this->error('No tenant found in database.');
            return 1;
        }

        $subscription = \App\Models\Subscription::create([
            'tenant_id' => $tenant->id,
            'plan' => $planKey,
            'status' => 'pending_payment',
            'starts_at' => now(),
        ]);

        $data = $service->buildCheckoutData($tenant, $subscription, $planConfig);
        $rawSecret = config('services.payhere_checkout.merchant_secret');

        $amount = $data['amount'];
        $orderId = $data['order_id'];
        $merchantId = $data['merchant_id'];
        $currency = $data['currency'];
        $decoded = base64_decode($rawSecret);

        $this->info('=== PayHere Checkout Debug ===');
        $this->newLine();
        $this->info('Form POST URL: ' . $data['action']);
        $this->info('Sandbox Mode: ' . (config('services.payhere_checkout.sandbox') ? 'YES' : 'NO'));
        $this->newLine();

        $this->info('--- Form Parameters ---');
        foreach ($data as $key => $value) {
            if ($key !== 'action') {
                $this->line("  {$key}: {$value}");
            }
        }
        $this->newLine();

        $this->info('--- Hash with BASE64 DECODED secret (PayHere official method) ---');
        $innerDecoded = strtoupper(md5($decoded));
        $this->line("  raw_secret (base64):  {$rawSecret}");
        $this->line("  decoded_secret:       {$decoded}");
        $this->line("  md5(decoded):           {$innerDecoded}");
        $concat = $merchantId . $orderId . $amount . $currency . $innerDecoded;
        $this->line("  concat:               {$concat}");
        $this->line("  FINAL HASH:           " . strtoupper(md5($concat)));
        $this->newLine();

        $this->info('Current hash in checkout form: ' . $data['hash']);
        $this->comment('(Using base64 decoded method as per PayHere official docs)');
        $this->newLine();

        $this->warn('=== TROUBLESHOOTING ===');
        $this->line('1. Use SANDBOX credentials from https://sandbox.payhere.lk (not live)');
        $this->line('2. Register your domain at: PayHere Dashboard > Settings > Domain/App Registration');
        $this->line('3. Test cards: Visa 4916217501611292, Master 5307732125531191');
        $this->newLine();

        $this->info('Copy this HTML to test manually:');
        $this->newLine();
        $this->line('<form method="POST" action="' . $data['action'] . '">');
        foreach ($data as $key => $value) {
            if ($key !== 'action') {
                $this->line('  <input type="hidden" name="' . $key . '" value="' . $value . '">');
            }
        }
        $this->line('  <button type="submit">Pay Now</button>');
        $this->line('</form>');

        $subscription->forceDelete();
        return 0;
    }
}