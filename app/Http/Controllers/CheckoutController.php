<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Address;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:credit_card,debit_card,paypal,cash_on_delivery',
            'notes' => 'nullable|string|max:500',
        ]);

        $address = Address::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cart = Cart::with(['cartItems.book'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $unavailableItems = [];
        foreach ($cart->cartItems as $item) {
            if (!$item->book->is_active || $item->book->stock_quantity < $item->quantity) {
                $unavailableItems[] = $item->book->title;
            }
        }

        if (!empty($unavailableItems)) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items are no longer available: ' . implode(', ', $unavailableItems));
        }

        $subtotal = $cart->cartItems->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $shippingCost = $subtotal >= 500 ? 0 : 50;
        $total = $subtotal + $shippingCost;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'order_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $total,
                'shipping_address' => [
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                ],
                'notes' => $request->notes,
            ]);

            foreach ($cart->cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'book_title' => $item->book->title,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->quantity * $item->unit_price,
                ]);

                $item->book->decrement('stock_quantity', $item->quantity);
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'pending',
                'amount' => $total,
                'transaction_id' => null,
            ]);

            $cart->cartItems()->delete();
            $cart->delete();

            DB::commit();

            if ($request->payment_method === 'cash_on_delivery') {
                return redirect()->route('orders.success', $order->id)
                    ->with('success', 'Order placed successfully!');
            }

            return redirect()->route('payment.process', [
                'order' => $order->id,
                'payment' => $payment->id,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function validateStock(Request $request): \Illuminate\Http\JsonResponse
    {
        $cart = Cart::with(['cartItems.book'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart) {
            return response()->json(['valid' => false, 'message' => 'Cart not found']);
        }

        $invalidItems = [];
        foreach ($cart->cartItems as $item) {
            if (!$item->book->is_active) {
                $invalidItems[] = $item->book->title . ' is no longer available';
            } elseif ($item->book->stock_quantity < $item->quantity) {
                $invalidItems[] = $item->book->title . ' has only ' . $item->book->stock_quantity . ' items left';
            }
        }

        return response()->json([
            'valid' => empty($invalidItems),
            'errors' => $invalidItems,
        ]);
    }

    public function calculateShipping(Request $request): \Illuminate\Http\JsonResponse
    {
        $subtotal = $request->get('subtotal', 0);
        $shippingCost = $subtotal >= 500 ? 0 : 50;

        return response()->json([
            'shipping_cost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
            'free_shipping_threshold' => 500,
        ]);
    }
}