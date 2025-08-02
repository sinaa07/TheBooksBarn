<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function process(Request $request, int $order, int $payment): Response
    {
        $order = Order::where('user_id', Auth::id())
            ->where('id', $order)
            ->firstOrFail();

        $payment = Payment::where('order_id', $order->id)
            ->where('id', $payment)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        return Inertia::render('Payment/Process', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
            ],
            'payment' => [
                'id' => $payment->id,
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
            ],
        ]);
    }

    public function complete(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'transaction_id' => 'nullable|string|max:255',
            'payment_data' => 'nullable|array',
        ]);

        $payment = Payment::with('order')
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $request->payment_id)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        $payment->update([
            'payment_status' => 'completed',
            'transaction_id' => $request->transaction_id ?? 'TXN-' . strtoupper(Str::random(10)),
            'completed_at' => now(),
        ]);

        $payment->order->update([
            'order_status' => 'confirmed',
        ]);

        return redirect()->route('orders.success', $payment->order->id)
            ->with('success', 'Payment completed successfully!');
    }

    public function failed(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'error_message' => 'nullable|string|max:500',
        ]);

        $payment = Payment::with('order')
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $request->payment_id)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        $payment->update([
            'payment_status' => 'failed',
            'notes' => $request->error_message,
        ]);

        return redirect()->route('cart.checkout')
            ->with('error', 'Payment failed. Please try again.');
    }

    public function webhook(Request $request): \Illuminate\Http\JsonResponse
    {
        $signature = $request->header('X-Payment-Signature');
        $payload = $request->getContent();
        
        if (!$this->verifyWebhookSignature($signature, $payload)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();
        
        $payment = Payment::where('transaction_id', $data['transaction_id'] ?? null)
            ->orWhere('id', $data['payment_id'] ?? null)
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        switch ($data['status']) {
            case 'success':
            case 'completed':
                $payment->update([
                    'payment_status' => 'completed',
                    'transaction_id' => $data['transaction_id'] ?? $payment->transaction_id,
                    'completed_at' => now(),
                    'notes' => $data['message'] ?? null,
                ]);
                
                $payment->order->update(['order_status' => 'confirmed']);
                break;

            case 'failed':
            case 'declined':
                $payment->update([
                    'payment_status' => 'failed',
                    'notes' => $data['message'] ?? 'Payment failed',
                ]);
                break;

            case 'refunded':
                $payment->update([
                    'payment_status' => 'refunded',
                    'notes' => $data['message'] ?? 'Payment refunded',
                ]);
                
                $payment->order->update(['order_status' => 'cancelled']);
                
                foreach ($payment->order->orderItems as $item) {
                    $item->book->increment('stock_quantity', $item->quantity);
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }

    public function refund(Request $request, int $paymentId): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment = Payment::with('order')
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $paymentId)
            ->where('payment_status', 'completed')
            ->firstOrFail();

        if (!in_array($payment->order->order_status, ['pending', 'confirmed', 'processing'])) {
            return back()->with('error', 'Refund not available for this order status.');
        }

        $refundSuccess = $this->processRefund($payment, $request->reason);

        if ($refundSuccess) {
            $payment->update([
                'payment_status' => 'refunded',
                'notes' => 'Refund requested: ' . $request->reason,
            ]);

            $payment->order->update(['order_status' => 'cancelled']);

            foreach ($payment->order->orderItems as $item) {
                $item->book->increment('stock_quantity', $item->quantity);
            }

            return back()->with('success', 'Refund processed successfully.');
        }

        return back()->with('error', 'Refund processing failed. Please contact support.');
    }

    public function retry(Request $request, int $paymentId): RedirectResponse
    {
        $payment = Payment::with('order')
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $paymentId)
            ->where('payment_status', 'failed')
            ->firstOrFail();

        $newPayment = Payment::create([
            'order_id' => $payment->order_id,
            'payment_method' => $payment->payment_method,
            'payment_status' => 'pending',
            'amount' => $payment->amount,
        ]);

        return redirect()->route('payment.process', [
            'order' => $payment->order_id,
            'payment' => $newPayment->id,
        ]);
    }

    public function status(Request $request, int $paymentId): \Illuminate\Http\JsonResponse
    {
        $payment = Payment::with('order')
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $paymentId)
            ->firstOrFail();

        return response()->json([
            'payment_status' => $payment->payment_status,
            'transaction_id' => $payment->transaction_id,
            'completed_at' => $payment->completed_at,
            'order_status' => $payment->order->order_status,
        ]);
    }

    private function verifyWebhookSignature(string $signature = null, string $payload = ''): bool
    {
        if (!$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, config('payment.webhook_secret', 'default_secret'));
        
        return hash_equals($expectedSignature, $signature);
    }

    private function processRefund(Payment $payment, string $reason): bool
    {
        switch ($payment->payment_method) {
            case 'credit_card':
            case 'debit_card':
                return $this->processCreditCardRefund($payment, $reason);
            case 'paypal':
                return $this->processPayPalRefund($payment, $reason);
            case 'cash_on_delivery':
                return true;
            default:
                return false;
        }
    }

    private function processCreditCardRefund(Payment $payment, string $reason): bool
    {
        return true;
    }

    private function processPayPalRefund(Payment $payment, string $reason): bool
    {
        return true;
    }
}