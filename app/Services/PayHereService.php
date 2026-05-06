<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Checkout (hosted form): reads only `services.payhere_checkout` (merchant_id + merchant_secret + hash).
 * Merchant API (OAuth, subscriptions): reads only `services.payhere_api`.
 */
class PayHereService
{
    private string $merchantId;

    private string $merchantSecret;

    private string $appId;

    private string $appSecret;

    /** Hosted checkout origin (sandbox or live). */
    private string $checkoutOrigin;

    /** Merchant REST API origin (sandbox or live). */
    private string $merchantApiOrigin;

    private string $currency;

    public function __construct()
    {
        $this->merchantId = trim((string) config('services.payhere_checkout.merchant_id', ''));
        $this->merchantSecret = trim((string) config('services.payhere_checkout.merchant_secret', ''));
        $this->currency = strtoupper(trim((string) config('services.payhere_checkout.currency', 'LKR')));
        $this->checkoutOrigin = self::payHereOrigin((bool) config('services.payhere_checkout.sandbox', true));
        $this->merchantApiOrigin = self::payHereOrigin((bool) config('services.payhere_api.sandbox', true));
        $this->appId = trim((string) config('services.payhere_api.app_id', ''));
        $this->appSecret = trim((string) config('services.payhere_api.app_secret', ''));
    }

    public function getBaseUrl(): string
    {
        return $this->checkoutOrigin;
    }

    private static function payHereOrigin(bool $sandbox): string
    {
        return $sandbox ? 'https://sandbox.payhere.lk' : 'https://www.payhere.lk';
    }

    public function generateHash(string $orderId, string $amount): string
    {
        if ($this->merchantId === '' || $this->merchantSecret === '') {
            throw new \RuntimeException('PayHere checkout credentials are missing.');
        }

        $orderId = trim((string) $orderId);
        $formattedAmount = number_format((float) $amount, 2, '.', '');
        $secretMd5Upper = strtoupper(md5($this->merchantSecret));
        $preimage = $this->merchantId.$orderId.$formattedAmount.$this->currency.$secretMd5Upper;
        $hash = strtoupper(md5($preimage));

        if ((bool) config('services.payhere_checkout.debug', false)) {
            Log::info('PAYHERE_HASH_INPUT', [
                'merchant_id' => $this->merchantId,
                'order_id' => $orderId,
                'amount' => $formattedAmount,
                'currency' => $this->currency,
                'secret_hash' => $secretMd5Upper,
                'lengths' => [
                    'merchant_id' => strlen($this->merchantId),
                    'order_id' => strlen($orderId),
                    'amount' => strlen($formattedAmount),
                    'currency' => strlen($this->currency),
                    'secret_hash' => strlen($secretMd5Upper),
                ],
                'order_id_json' => json_encode($orderId, JSON_THROW_ON_ERROR),
            ]);
            Log::info('PAYHERE_HASH_STRING', ['string' => $preimage]);
            Log::info('PAYHERE_HASH_RESULT', ['hash' => $hash]);
        }

        return $hash;
    }

    public function verifyHash(array $data): bool
    {
        $localHash = strtoupper(md5(
            $this->merchantId
            . ($data['order_id'] ?? '')
            . ($data['payhere_amount'] ?? '')
            . $this->currency
            . ($data['status_code'] ?? '')
            . strtoupper(md5($this->merchantSecret))
        ));

        return $localHash === ($data['md5sig'] ?? '');
    }

    /**
     * OAuth access token for Subscription Manager / Merchant APIs (client_credentials + Basic auth).
     */
    public function getAccessToken(bool $forceRefresh = false): ?string
    {
        $cacheKey = $this->oauthCacheKey();

        if (! $forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $basic = $this->merchantBasicAuthorizationValue();
        if ($basic === null) {
            Log::error('PayHere OAuth: missing PAYHERE_APP_ID/PAYHERE_APP_SECRET or PAYHERE_MERCHANT_BASIC_AUTH');

            return null;
        }

        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic '.$basic,
            ])
            ->post("{$this->merchantApiOrigin}/merchant/v1/oauth/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            Log::error('PayHere OAuth token failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $body = $response->json();
        $token = $body['access_token'] ?? null;
        if (! is_string($token) || $token === '') {
            Log::error('PayHere OAuth: no access_token in response', ['body' => $response->body()]);

            return null;
        }

        $expiresIn = (int) ($body['expires_in'] ?? 600);
        $ttl = max(30, $expiresIn - 60);
        Cache::put($cacheKey, $token, now()->addSeconds($ttl));

        return $token;
    }

    public function forgetCachedAccessToken(): void
    {
        Cache::forget($this->oauthCacheKey());
    }

    /**
     * GET /merchant/v1/subscription — list all recurring subscriptions.
     *
     * @return array<string, mixed>
     */
    public function listSubscriptions(): array
    {
        return $this->merchantApiGet('/merchant/v1/subscription');
    }

    /**
     * GET /merchant/v1/subscription/{subscription_id}/payments
     *
     * @return array<string, mixed>
     */
    public function getSubscriptionPayments(string|int $payhereSubscriptionId): array
    {
        $id = rawurlencode((string) $payhereSubscriptionId);

        return $this->merchantApiGet("/merchant/v1/subscription/{$id}/payments");
    }

    /**
     * POST /merchant/v1/subscription/retry
     *
     * @return array<string, mixed>
     */
    public function retrySubscription(string|int $payhereSubscriptionId): array
    {
        return $this->merchantApiPost('/merchant/v1/subscription/retry', [
            'subscription_id' => is_numeric($payhereSubscriptionId)
                ? (int) $payhereSubscriptionId
                : $payhereSubscriptionId,
        ]);
    }

    /**
     * POST /merchant/v1/subscription/cancel (Subscription Manager API).
     *
     * @return array<string, mixed>
     */
    public function cancelSubscriptionViaApi(string|int $payhereSubscriptionId): array
    {
        return $this->merchantApiPost('/merchant/v1/subscription/cancel', [
            'subscription_id' => is_numeric($payhereSubscriptionId)
                ? (int) $payhereSubscriptionId
                : $payhereSubscriptionId,
        ]);
    }

    public function subscriptionApiSucceeded(array $response): bool
    {
        return isset($response['status']) && (int) $response['status'] === 1;
    }

    public function createSubscription(Tenant $tenant, string $plan): ?array
    {
        $plans = config('plans', []);
        $planConfig = $plans[$plan] ?? null;

        if (! $planConfig) {
            return null;
        }

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan' => $plan,
            'status' => 'pending_payment',
            'starts_at' => now(),
        ]);

        return [
            'subscription' => $subscription,
            'checkout_data' => $this->buildCheckoutData($tenant, $subscription, $planConfig),
        ];
    }

    public function buildCheckoutData(Tenant $tenant, Subscription $subscription, array $planConfig): array
    {
        $amount = number_format((float) $planConfig['price_lkr'] / 100, 2, '.', '');
        $hash = $this->generateHash((string) $subscription->id, $amount);
        $returnUrl = (string) (config('services.payhere_checkout.return_url') ?: route('billing.return'));
        $cancelUrl = (string) (config('services.payhere_checkout.cancel_url') ?: route('billing.cancel'));
        $notifyUrl = (string) (config('services.payhere_checkout.notify_url') ?: route('payhere.webhook'));

        $checkoutData = [
            'action' => "{$this->checkoutOrigin}/pay/checkout",
            'merchant_id' => $this->merchantId,
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
            'notify_url' => $notifyUrl,
            'order_id' => (string) $subscription->id,
            'items' => $planConfig['name'].' Plan - Monthly',
            'currency' => $this->currency,
            'amount' => $amount,
            'first_name' => explode(' ', $tenant->name)[0],
            'last_name' => explode(' ', $tenant->name.' ')[1] ?? 'N/A',
            'email' => $tenant->billing_email ?? $tenant->email,
            'phone' => $tenant->phone ?? '',
            'address' => $tenant->address ?? '',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
            'hash' => $hash,
            'custom_1' => (string) $tenant->id,
            'recurring' => '1',
            'recurrence' => '1 Month',
            'duration' => 'Forever',
        ];

        if ((bool) config('services.payhere_checkout.debug', false)) {
            Log::info('PayHere checkout payload debug', [
                'action' => $checkoutData['action'],
                'merchant_id' => $checkoutData['merchant_id'],
                'order_id' => $checkoutData['order_id'],
                'amount' => $checkoutData['amount'],
                'currency' => $checkoutData['currency'],
                'hash' => $checkoutData['hash'],
                'has_api_fields' => isset($checkoutData['app_id'], $checkoutData['app_secret'], $checkoutData['basic_auth']),
            ]);
        }

        return $checkoutData;
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        if (! $subscription->payhere_subscription_id) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return true;
        }

        $payload = $this->cancelSubscriptionViaApi($subscription->payhere_subscription_id);

        if ($this->subscriptionApiSucceeded($payload)) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return true;
        }

        Log::error('PayHere subscription cancellation failed', [
            'subscription_id' => $subscription->id,
            'payhere_subscription_id' => $subscription->payhere_subscription_id,
            'response' => $payload,
        ]);

        return false;
    }

    public function recordPayment(Tenant $tenant, array $payhereData): Payment
    {
        $subscription = Subscription::find($payhereData['order_id'] ?? null);

        return Payment::create([
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription?->id,
            'payhere_payment_id' => $payhereData['payment_id'] ?? null,
            'amount' => $payhereData['payhere_amount'] ?? 0,
            'currency' => $payhereData['payhere_currency'] ?? $this->currency,
            'status' => $this->mapPayhereStatus($payhereData['status_code'] ?? ''),
            'payment_method' => $payhereData['payment_method'] ?? null,
            'plan' => $subscription?->plan,
            'payhere_response' => $payhereData,
            'paid_at' => now(),
        ]);
    }

    public function mapPayhereStatus(string $statusCode): string
    {
        return match ($statusCode) {
            '2' => 'successful',
            '0' => 'pending',
            '-1', '-2', '-3' => 'failed',
            default => 'pending',
        };
    }

    public function activateSubscription(Subscription $subscription, array $payhereData): void
    {
        $subscription->update([
            'payhere_subscription_id' => $payhereData['subscription_id'] ?? $payhereData['payment_id'] ?? null,
            'payhere_status' => $payhereData['status_code'] ?? '2',
            'status' => 'active',
            'starts_at' => $subscription->starts_at ?? now(),
            'ends_at' => now()->addMonth(),
        ]);

        $tenant = $subscription->tenant;
        $tenant->update([
            'status' => 'active',
            'plan' => $subscription->plan,
        ]);
    }

    private function oauthCacheKey(): string
    {
        $fingerprint = $this->merchantBasicAuthorizationValue() ?? 'none';

        return 'payhere.merchant_token.'.md5($this->merchantApiOrigin.'|'.$fingerprint);
    }

    /**
     * Base64 payload for Authorization: Basic (App ID:App Secret), without the "Basic " prefix.
     */
    private function merchantBasicAuthorizationValue(): ?string
    {
        $preencoded = config('services.payhere_api.basic_auth');
        if (is_string($preencoded) && trim($preencoded) !== '') {
            return preg_replace('/\s+/', '', trim($preencoded));
        }

        if ($this->appId !== '' && $this->appSecret !== '') {
            return base64_encode($this->appId.':'.$this->appSecret);
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function merchantApiGet(string $path): array
    {
        return $this->sendMerchantRequest(fn (?string $token) => Http::withToken((string) $token)
            ->acceptJson()
            ->get($this->merchantApiOrigin.$path));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function merchantApiPost(string $path, array $body): array
    {
        return $this->sendMerchantRequest(fn (?string $token) => Http::withToken((string) $token)
            ->acceptJson()
            ->post($this->merchantApiOrigin.$path, $body));
    }

    /**
     * @param  callable(?string): Response  $send
     * @return array<string, mixed>
     */
    private function sendMerchantRequest(callable $send): array
    {
        $token = $this->getAccessToken();
        if ($token === null) {
            return ['status' => -1, 'msg' => 'PayHere OAuth failed', 'data' => null];
        }

        $response = $send($token);
        $decoded = $response->json();

        if ($this->shouldRefreshTokenFromResponse($decoded)) {
            $this->forgetCachedAccessToken();
            $token = $this->getAccessToken(true);
            if ($token === null) {
                return ['status' => -1, 'msg' => 'PayHere OAuth failed after token refresh', 'data' => null];
            }
            $response = $send($token);
            $decoded = $response->json();
        }

        if (! is_array($decoded)) {
            Log::warning('PayHere Merchant API: non-JSON or invalid response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['status' => -1, 'msg' => 'Invalid response from PayHere', 'data' => null];
        }

        return $decoded;
    }

    /**
     * @param  mixed  $decoded
     */
    private function shouldRefreshTokenFromResponse($decoded): bool
    {
        if (! is_array($decoded)) {
            return false;
        }

        return isset($decoded['error']) && $decoded['error'] === 'invalid_token';
    }
}
