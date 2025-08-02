<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Set expiration time when cart is created (30 days from now)
        static::creating(function ($cart) {
            if (!$cart->expires_at) {
                $cart->expires_at = Carbon::now()->addDays(30);
            }
        });
    }

    // Relationships

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all cart items.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get cart items with their associated books.
     */
    public function itemsWithBooks()
    {
        return $this->cartItems()->with(['book' => function ($query) {
            $query->select('id', 'title', 'author', 'price', 'cover_image_url', 'stock_quantity', 'is_active');
        }]);
    }

    // Accessors

    /**
     * Get the total number of items in the cart.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->cartItems()->sum('quantity') ?? 0;
    }

    /**
     * Get the total value of items in the cart.
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->cartItems()
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0.0;
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    /**
     * Get the total weight of items (if needed for shipping).
     */
    public function getTotalWeightAttribute(): float
    {
        // Assuming average book weight of 0.3kg, you can customize this
        return $this->total_items * 0.3;
    }

    /**
     * Check if cart is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get cart summary for display.
     */
    public function getSummaryAttribute(): array
    {
        return [
            'total_items' => $this->total_items,
            'total_amount' => $this->total_amount,
            'formatted_total' => $this->formatted_total,
            'is_empty' => $this->isEmpty(),
            'has_unavailable_items' => $this->hasUnavailableItems(),
        ];
    }

    // Scopes

    /**
     * Scope a query to only include active carts.
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope a query to only include expired carts.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    /**
     * Scope a query to only include non-empty carts.
     */
    public function scopeNotEmpty($query)
    {
        return $query->has('cartItems');
    }

    // Helper Methods

    /**
     * Check if the cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->cartItems()->count() === 0;
    }

    /**
     * Check if the cart is expired.
     */
    public function isExpired(): bool
    {
        return $this->is_expired;
    }

    /**
     * Extend cart expiration.
     */
    public function extendExpiration(int $days = 30): bool
    {
        return $this->update([
            'expires_at' => Carbon::now()->addDays($days)
        ]);
    }

    /**
     * Add a book to the cart.
     */
    public function addBook(Book $book, int $quantity = 1): CartItem|bool
    {
        // Check if book is available
        if (!$book->canAddToCart($quantity)) {
            return false;
        }

        // Check if item already exists in cart
        $existingItem = $this->cartItems()->where('book_id', $book->id)->first();

        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem->quantity + $quantity;
            
            if (!$book->canAddToCart($newQuantity)) {
                return false;
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $book->price, // Update price in case it changed
            ]);

            return $existingItem;
        }

        // Create new cart item
        return $this->cartItems()->create([
            'book_id' => $book->id,
            'quantity' => $quantity,
            'unit_price' => $book->price,
        ]);
    }

    /**
     * Remove a book from the cart.
     */
    public function removeBook(Book $book): bool
    {
        return $this->cartItems()->where('book_id', $book->id)->delete() > 0;
    }

    /**
     * Update quantity of a book in the cart.
     */
    public function updateBookQuantity(Book $book, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeBook($book);
        }

        if (!$book->canAddToCart($quantity)) {
            return false;
        }

        $cartItem = $this->cartItems()->where('book_id', $book->id)->first();

        if (!$cartItem) {
            return false;
        }

        return $cartItem->update([
            'quantity' => $quantity,
            'unit_price' => $book->price, // Update price in case it changed
        ]);
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): bool
    {
        return $this->cartItems()->delete() > 0;
    }

    /**
     * Get unavailable items (out of stock or inactive books).
     */
    public function getUnavailableItems()
    {
        return $this->itemsWithBooks()
            ->whereHas('book', function ($query) {
                $query->where('is_active', false)
                      ->orWhere('stock_quantity', '<=', 0);
            })
            ->get();
    }

    /**
     * Check if cart has unavailable items.
     */
    public function hasUnavailableItems(): bool
    {
        return $this->getUnavailableItems()->count() > 0;
    }

    /**
     * Remove unavailable items from cart.
     */
    public function removeUnavailableItems(): int
    {
        $unavailableItems = $this->getUnavailableItems();
        $removedCount = $unavailableItems->count();

        foreach ($unavailableItems as $item) {
            $item->delete();
        }

        return $removedCount;
    }

    /**
     * Get items with insufficient stock.
     */
    public function getInsufficientStockItems()
    {
        return $this->itemsWithBooks()
            ->get()
            ->filter(function ($item) {
                return $item->book && $item->quantity > $item->book->stock_quantity;
            });
    }

    /**
     * Fix insufficient stock items by adjusting quantities.
     */
    public function fixInsufficientStock(): int
    {
        $fixedCount = 0;
        $insufficientItems = $this->getInsufficientStockItems();

        foreach ($insufficientItems as $item) {
            if ($item->book->stock_quantity > 0) {
                $item->update(['quantity' => $item->book->stock_quantity]);
                $fixedCount++;
            } else {
                $item->delete();
                $fixedCount++;
            }
        }

        return $fixedCount;
    }

    /**
     * Validate cart before checkout.
     */
    public function validateForCheckout(): array
    {
        $errors = [];

        if ($this->isEmpty()) {
            $errors[] = 'Cart is empty';
        }

        if ($this->isExpired()) {
            $errors[] = 'Cart has expired';
        }

        $unavailableItems = $this->getUnavailableItems();
        if ($unavailableItems->count() > 0) {
            $errors[] = 'Cart contains unavailable items';
        }

        $insufficientStockItems = $this->getInsufficientStockItems();
        if ($insufficientStockItems->count() > 0) {
            $errors[] = 'Some items have insufficient stock';
        }

        return $errors;
    }

    /**
     * Check if cart is valid for checkout.
     */
    public function isValidForCheckout(): bool
    {
        return empty($this->validateForCheckout());
    }

    /**
     * Get cart items grouped by category.
     */
    public function getItemsByCategory()
    {
        return $this->itemsWithBooks()
            ->with('book.category')
            ->get()
            ->groupBy('book.category.category_name');
    }

    /**
     * Calculate estimated shipping cost (placeholder logic).
     */
    public function getEstimatedShipping(): float
    {
        // Simple shipping calculation - you can customize this
        if ($this->total_amount >= 500) {
            return 0.0; // Free shipping over ₹500
        }
        
        return 50.0; // Flat rate shipping
    }

    /**
     * Get cart data for API/frontend.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'items' => $this->itemsWithBooks()->get(),
            'summary' => $this->summary,
            'estimated_shipping' => $this->getEstimatedShipping(),
        ]);
    }

    /**
     * Clean up expired carts (for scheduled jobs).
     */
    public static function cleanupExpired(): int
    {
        return static::expired()->delete();
    }
}