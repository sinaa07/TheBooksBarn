<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->get('status');
        
        $ordersQuery = Order::with(['orderItems.book', 'payment', 'shipment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($status) {
            $ordersQuery->where('order_status', $status);
        }

        $orders = $ordersQuery->paginate(10)->appends($request->query());

        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'subtotal' => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at,
                'shipped_at' => $order->shipped_at,
                'delivered_at' => $order->delivered_at,
                'items_count' => $order->orderItems->count(),
                'payment_status' => $order->payment?->payment_status,
                'payment_method' => $order->payment?->payment_method,
                'tracking_number' => $order->shipment?->tracking_number,
            ];
        });

        $statusCounts = [
            'all' => Order::where('user_id', Auth::id())->count(),
            'pending' => Order::where('user_id', Auth::id())->where('order_status', 'pending')->count(),
            'confirmed' => Order::where('user_id', Auth::id())->where('order_status', 'confirmed')->count(),
            'processing' => Order::where('user_id', Auth::id())->where('order_status', 'processing')->count(),
            'shipped' => Order::where('user_id', Auth::id())->where('order_status', 'shipped')->count(),
            'delivered' => Order::where('user_id', Auth::id())->where('order_status', 'delivered')->count(),
            'cancelled' => Order::where('user_id', Auth::id())->where('order_status', 'cancelled')->count(),
        ];

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $order = Order::with(['orderItems.book.category', 'payment', 'shipment'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $orderItems = $order->orderItems->map(function ($item) {
            return [
                'id' => $item->id,
                'book_title' => $item->book_title,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'book' => [
                    'id' => $item->book->id,
                    'title' => $item->book->title,
                    'author' => $item->book->author,
                    'cover_image_url' => $item->book->cover_image_url,
                    'format' => $item->book->format,
                    'category' => $item->book->category?->category_name,
                ],
            ];
        });

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'subtotal' => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'total_amount' => $order->total_amount,
                'shipping_address' => $order->shipping_address,
                'notes' => $order->notes,
                'created_at' => $order->created_at,
                'shipped_at' => $order->shipped_at,
                'delivered_at' => $order->delivered_at,
            ],
            'orderItems' => $orderItems,
            'payment' => $order->payment ? [
                'id' => $order->payment->id,
                'payment_method' => $order->payment->payment_method,
                'payment_status' => $order->payment->payment_status,
                'amount' => $order->payment->amount,
                'transaction_id' => $order->payment->transaction_id,
                'completed_at' => $order->payment->completed_at,
            ] : null,
            'shipment' => $order->shipment ? [
                'id' => $order->shipment->id,
                'tracking_number' => $order->shipment->tracking_number,
                'carrier' => $order->shipment->carrier,
                'shipment_status' => $order->shipment->shipment_status,
                'shipped_at' => $order->shipment->shipped_at,
                'delivered_at' => $order->shipment->delivered_at,
                'notes' => $order->shipment->notes,
            ] : null,
        ]);
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $order = Order::where('user_id', Auth::id())
            ->where('id', $id)
            ->whereIn('order_status', ['pending', 'confirmed'])
            ->firstOrFail();

        $order->update(['order_status' => 'cancelled']);

        foreach ($order->orderItems as $item) {
            $item->book->increment('stock_quantity', $item->quantity);
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order cancelled successfully.');
    }

    public function success(Request $request, int $id): Response
    {
        $order = Order::with(['orderItems.book', 'payment'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return Inertia::render('Orders/Success', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at,
                'items_count' => $order->orderItems->count(),
                'payment_method' => $order->payment?->payment_method,
                'payment_status' => $order->payment?->payment_status,
            ],
        ]);
    }

    public function track(Request $request, string $orderNumber): Response
    {
        $order = Order::with(['shipment'])
            ->where('user_id', Auth::id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $trackingHistory = [];
        
        if ($order->created_at) {
            $trackingHistory[] = [
                'status' => 'Order Placed',
                'date' => $order->created_at,
                'description' => 'Your order has been placed successfully.',
            ];
        }

        if ($order->order_status === 'confirmed') {
            $trackingHistory[] = [
                'status' => 'Order Confirmed',
                'date' => $order->updated_at,
                'description' => 'Your order has been confirmed and is being prepared.',
            ];
        }

        if ($order->order_status === 'processing') {
            $trackingHistory[] = [
                'status' => 'Processing',
                'date' => $order->updated_at,
                'description' => 'Your order is being processed.',
            ];
        }

        if ($order->shipped_at) {
            $trackingHistory[] = [
                'status' => 'Shipped',
                'date' => $order->shipped_at,
                'description' => 'Your order has been shipped.',
            ];
        }

        if ($order->delivered_at) {
            $trackingHistory[] = [
                'status' => 'Delivered',
                'date' => $order->delivered_at,
                'description' => 'Your order has been delivered.',
            ];
        }

        return Inertia::render('Orders/Track', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at,
                'shipped_at' => $order->shipped_at,
                'delivered_at' => $order->delivered_at,
            ],
            'shipment' => $order->shipment ? [
                'tracking_number' => $order->shipment->tracking_number,
                'carrier' => $order->shipment->carrier,
                'shipment_status' => $order->shipment->shipment_status,
            ] : null,
            'trackingHistory' => $trackingHistory,
        ]);
    }

    public function reorder(Request $request, int $id): RedirectResponse
    {
        $order = Order::with(['orderItems.book'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cart = \App\Models\Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['expires_at' => now()->addDays(7)]
        );

        foreach ($order->orderItems as $item) {
            if ($item->book->is_active && $item->book->stock_quantity > 0) {
                $existingItem = \App\Models\CartItem::where('cart_id', $cart->id)
                    ->where('book_id', $item->book_id)
                    ->first();

                $quantity = min($item->quantity, $item->book->stock_quantity);

                if ($existingItem) {
                    $newQuantity = min($existingItem->quantity + $quantity, $item->book->stock_quantity);
                    $existingItem->update([
                        'quantity' => $newQuantity,
                        'unit_price' => $item->book->price,
                    ]);
                } else {
                    \App\Models\CartItem::create([
                        'cart_id' => $cart->id,
                        'book_id' => $item->book_id,
                        'quantity' => $quantity,
                        'unit_price' => $item->book->price,
                    ]);
                }
            }
        }

        return redirect()->route('cart.index')
            ->with('success', 'Items added to cart successfully.');
    }
}