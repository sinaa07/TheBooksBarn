<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(): Response
    {
        $cart = Cart::with(['cartItems.book.category'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return Inertia::render('Cart/Index', [
                'cart' => null,
                'items' => [],
                'summary' => [
                    'subtotal' => 0,
                    'total' => 0,
                    'item_count' => 0,
                ],
            ]);
        }

        $items = $cart->cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->quantity * $item->unit_price,
                'book' => [
                    'id' => $item->book->id,
                    'title' => $item->book->title,
                    'author' => $item->book->author,
                    'cover_image_url' => $item->book->cover_image_url,
                    'format' => $item->book->format,
                    'stock_quantity' => $item->book->stock_quantity,
                    'category' => $item->book->category?->category_name,
                ],
            ];
        });

        $subtotal = $items->sum('total_price');
        $itemCount = $items->sum('quantity');

        return Inertia::render('Cart/Index', [
            'cart' => [
                'id' => $cart->id,
                'expires_at' => $cart->expires_at,
            ],
            'items' => $items,
            'summary' => [
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'item_count' => $itemCount,
            ],
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $book = Book::where('id', $request->book_id)
            ->where('is_active', true)
            ->where('stock_quantity', '>=', $request->quantity)
            ->firstOrFail();

        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['expires_at' => now()->addDays(7)]
        );

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('book_id', $book->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            if ($newQuantity > $book->stock_quantity) {
                return back()->with('error', 'Not enough stock available.');
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $book->price,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'book_id' => $book->id,
                'quantity' => $request->quantity,
                'unit_price' => $book->price,
            ]);
        }

        return back()->with('success', 'Book added to cart successfully.');
    }

    public function update(Request $request, int $itemId): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cartItem = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $itemId)
            ->with('book')
            ->firstOrFail();

        if ($request->quantity > $cartItem->book->stock_quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'unit_price' => $cartItem->book->price,
        ]);

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove(int $itemId): RedirectResponse
    {
        $cartItem = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $itemId)
            ->firstOrFail();

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->cartItems()->delete();
        }

        return back()->with('success', 'Cart cleared successfully.');
    }

    public function count(): \Illuminate\Http\JsonResponse
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        
        $count = 0;
        if ($cart) {
            $count = $cart->cartItems()->sum('quantity');
        }

        return response()->json(['count' => $count]);
    }

    public function checkout(): Response
    {
        $cart = Cart::with(['cartItems.book.category'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $unavailableItems = [];
        $items = $cart->cartItems->map(function ($item) use (&$unavailableItems) {
            if (!$item->book->is_active || $item->book->stock_quantity < $item->quantity) {
                $unavailableItems[] = $item->book->title;
            }

            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->quantity * $item->unit_price,
                'book' => [
                    'id' => $item->book->id,
                    'title' => $item->book->title,
                    'author' => $item->book->author,
                    'cover_image_url' => $item->book->cover_image_url,
                    'format' => $item->book->format,
                    'stock_quantity' => $item->book->stock_quantity,
                    'is_active' => $item->book->is_active,
                ],
            ];
        });

        if (!empty($unavailableItems)) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items in your cart are no longer available: ' . implode(', ', $unavailableItems));
        }

        $subtotal = $items->sum('total_price');
        $shippingCost = $subtotal >= 500 ? 0 : 50;
        $total = $subtotal + $shippingCost;

        $addresses = Auth::user()->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'is_default' => $address->is_default,
                ];
            });

        return Inertia::render('Cart/Checkout', [
            'cart' => [
                'id' => $cart->id,
            ],
            'items' => $items,
            'addresses' => $addresses,
            'summary' => [
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'item_count' => $items->sum('quantity'),
            ],
            'payment_methods' => [
                'credit_card' => 'Credit Card',
                'debit_card' => 'Debit Card',
                'paypal' => 'PayPal',
                'cash_on_delivery' => 'Cash on Delivery',
            ],
        ]);
    }
}