<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService
{
    protected ?Api $api = null;
    protected string $key;
    protected string $secret;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->key = (string) (config('services.razorpay.key') ?? env('RAZORPAY_KEY', ''));
        $this->secret = (string) (config('services.razorpay.secret') ?? env('RAZORPAY_SECRET', ''));
        $this->webhookSecret = (string) (config('services.razorpay.webhook_secret') ?? env('RAZORPAY_WEBHOOK_SECRET', ''));

        if (!empty($this->key) && !empty($this->secret) && $this->key !== 'rzp_test_placeholder') {
            $this->api = new Api($this->key, $this->secret);
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function isConfigured(): bool
    {
        return $this->api !== null;
    }

    /**
     * Create a Razorpay Order for an application order.
     *
     * @param Order $order
     * @return array{id: string, amount: int, currency: string}
     */
    public function createOrder(Order $order): array
    {
        $amountInPaise = (int) round($order->grand_total * 100);

        if ($this->api) {
            try {
                $razorpayOrder = $this->api->order->create([
                    'receipt' => (string) $order->order_number,
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'notes' => [
                        'order_id' => (string) $order->id,
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                    ],
                ]);

                return [
                    'id' => $razorpayOrder['id'],
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                ];
            } catch (Exception $e) {
                Log::error('Razorpay order creation failed: ' . $e->getMessage());
                throw $e;
            }
        }

        // Development / Test mode fallback when real credentials are placeholder
        return [
            'id' => 'order_demo_' . bin2hex(random_bytes(8)),
            'amount' => $amountInPaise,
            'currency' => 'INR',
        ];
    }

    /**
     * Verify the payment signature server-side.
     *
     * @param string $razorpayOrderId
     * @param string $razorpayPaymentId
     * @param string $razorpaySignature
     * @return bool
     */
    public function verifySignature(string $razorpayOrderId, string $razorpayPaymentId, string $razorpaySignature): bool
    {
        if (str_starts_with($razorpayOrderId, 'order_demo_')) {
            return true; // Test mode mock order verification
        }

        if (!$this->api) {
            return true;
        }

        try {
            $attributes = [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ];

            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay signature verification failed: ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            Log::error('Razorpay verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify a webhook signature from Razorpay.
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (empty($this->webhookSecret) || !$this->api) {
            return false;
        }

        try {
            $this->api->utility->verifyWebhookSignature($payload, $signature, $this->webhookSecret);
            return true;
        } catch (Exception $e) {
            Log::warning('Razorpay webhook signature verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
