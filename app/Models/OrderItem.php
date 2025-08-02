<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'book_id',
        'book_title',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-calculate total price before saving
        static::saving(function ($orderItem) {
            $orderItem->total_price = $orderItem->quantity * $orderItem->unit_price;
            
            // Store book title for historical record if not provided
            if (empty($orderItem->book_title) && $orderItem->book) {
                $orderItem->book_title = $orderItem->book->title;
            }
        });
    }

    // Relationships

    /**
     * Get the order that owns the order item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the book associated with the order item.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Accessors

    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '₹' . number_format($this->unit_price, 2);
    }

    /**
     * Get the formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '₹' . number_format($this->total_price, 2);
    }

    /**
     * Get the book title (use stored title or current book title).
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->book_title ?: ($this->book?->title ?? 'Unknown Book');
    }

    /**
     * Get the current book price to compare with order price.
     */
    public function getCurrentBookPriceAttribute(): ?float
    {
        return $this->book?->price;
    }

    /**
     * Check if the book price has changed since the order.
     */
    public function getHasPriceChangedAttribute(): bool
    {
        if (!$this->book) {
            return false;
        }
        
        return $this->unit_price != $this->book->price;
    }

    /**
     * Get the price difference (current - order price).
     */
    public function getPriceDifferenceAttribute(): float
    {
        if (!$this->book) {
            return 0;
        }
        
        return $this->book->price - $this->unit_price;
    }

    /**
     * Get the total savings or additional cost.
     */
    public function getTotalPriceDifferenceAttribute(): float
    {
        return $this->price_difference * $this->quantity;
    }

    /**
     * Check if the book is still available.
     */
    public function getIsBookAvailableAttribute(): bool
    {
        return $this->book && $this->book->is_active;
    }

    /**
     * Check if the book is still in stock.
     */
    public function getIsBookInStockAttribute(): bool
    {
        return $this->book && $this->book->stock_quantity > 0;
    }

    /**
     * Get the book's current stock quantity.
     */
    public function getCurrentStockAttribute(): int
    {
        return $this->book?->stock_quantity ?? 0;
    }

    /**
     * Get line item weight (for shipping calculations).
     */
    public function getLineWeightAttribute(): float
    {
        // Assuming average book weight of 0.3kg per book
        return $this->quantity * 0.3;
    }

    // Scopes

    /**
     * Scope a query to filter by order status.
     */
    public function scopeByOrderStatus($query, string $status)
    {
        return $query->whereHas('order', function ($orderQuery) use ($status) {
            $orderQuery->where('order_status', $status);
        });
    }

    /**
     * Scope a query to only include delivered items.
     */
    public function scopeDelivered($query)
    {
        return $query->byOrderStatus('delivered');
    }

    /**
     * Scope a query to only include shipped items.
     */
    public function scopeShipped($query)
    {
        return $query->byOrderStatus('shipped');
    }

    /**
     * Scope a query to only include cancelled items.
     */
    public function scopeCancelled($query)
    {
        return $query->byOrderStatus('cancelled');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereHas('order', function ($orderQuery) use ($startDate, $endDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        });
    }

    /**
     * Scope a query to filter by book.
     */
    public function scopeForBook($query, $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopePriceRange($query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('unit_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to search by book title.
     */
    public function scopeSearchByTitle($query, string $term)
    {
        return $query->where('book_title', 'LIKE', "%{$term}%")
                    ->orWhereHas('book', function ($bookQuery) use ($term) {
                        $bookQuery->where('title', 'LIKE', "%{$term}%");
                    });
    }

    /**
     * Scope a query to order by total price.
     */
    public function scopeOrderByTotalPrice($query, string $direction = 'desc')
    {
        return $query->orderBy('total_price', $direction);
    }

    /**
     * Scope a query to order by quantity.
     */
    public function scopeOrderByQuantity($query, string $direction = 'desc')
    {
        return $query->orderBy('quantity', $direction);
    }

    // Helper Methods

    /**
     * Check if the book price has changed.
     */
    public function hasPriceChanged(): bool
    {
        return $this->has_price_changed;
    }

    /**
     * Check if the book is still available.
     */
    public function isBookAvailable(): bool
    {
        return $this->is_book_available;
    }

    /**
     * Check if the book is still in stock.
     */
    public function isBookInStock(): bool
    {
        return $this->is_book_in_stock;
    }

    /**
     * Check if this item can be reordered.
     */
    public function canBeReordered(): bool
    {
        return $this->book && $this->book->isAvailable() && $this->book->stock_quantity >= $this->quantity;
    }

    /**
     * Get the maximum quantity that can be reordered.
     */
    public function getMaxReorderQuantity(): int
    {
        if (!$this->book || !$this->book->isAvailable()) {
            return 0;
        }
        
        return min($this->quantity, $this->book->stock_quantity);
    }

    /**
     * Calculate line total with current book price.
     */
    public function calculateCurrentLineTotal(): float
    {
        if (!$this->book) {
            return $this->total_price;
        }
        
        return $this->quantity * $this->book->price;
    }

    /**
     * Get savings or additional cost with current pricing.
     */
    public function getCurrentPricingDifference(): array
    {
        $currentTotal = $this->calculateCurrentLineTotal();
        $difference = $currentTotal - $this->total_price;
        
        return [
            'original_total' => $this->total_price,
            'current_total' => $currentTotal,
            'difference' => $difference,
            'is_savings' => $difference < 0,
            'is_increase' => $difference > 0,
            'formatted_original' => $this->formatted_total_price,
            'formatted_current' => '₹' . number_format($currentTotal, 2),
            'formatted_difference' => '₹' . number_format(abs($difference), 2),
        ];
    }

    /**
     * Create a return/refund record (placeholder for future enhancement).
     */
    public function createReturn(int $quantity, string $reason): bool
    {
        // This is a placeholder - you might want to create a separate returns table
        if ($quantity > $this->quantity || $quantity <= 0) {
            return false;
        }
        
        // Logic for handling returns would go here
        // For now, just update the order notes
        $this->order->update([
            'notes' => ($this->order->notes ?? '') . "\nReturn requested for {$quantity} x {$this->book_title}: {$reason}"
        ]);
        
        return true;
    }

    /**
     * Add to cart for reordering.
     */
    public function addToCart(int $quantity = null): bool
    {
        if (!$this->canBeReordered()) {
            return false;
        }
        
        $reorderQuantity = $quantity ?? min($this->quantity, $this->book->stock_quantity);
        
        if ($reorderQuantity <= 0) {
            return false;
        }
        
        $cart = $this->order->user->cart;
        
        if (!$cart) {
            return false;
        }
        
        return $cart->addBook($this->book, $reorderQuantity) !== false;
    }

    /**
     * Get analytics data for this item.
     */
    public function getAnalyticsData(): array
    {
        return [
            'book_id' => $this->book_id,
            'book_title' => $this->display_title,
            'quantity_sold' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_revenue' => $this->total_price,
            'order_date' => $this->order->created_at,
            'order_status' => $this->order->order_status,
            'customer_id' => $this->order->user_id,
        ];
    }

    /**
     * Get item data for API/frontend.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'formatted_unit_price' => $this->formatted_unit_price,
            'formatted_total_price' => $this->formatted_total_price,
            'display_title' => $this->display_title,
            'current_book_price' => $this->current_book_price,
            'has_price_changed' => $this->has_price_changed,
            'price_difference' => $this->price_difference,
            'total_price_difference' => $this->total_price_difference,
            'is_book_available' => $this->is_book_available,
            'is_book_in_stock' => $this->is_book_in_stock,
            'current_stock' => $this->current_stock,
            'can_be_reordered' => $this->canBeReordered(),
            'max_reorder_quantity' => $this->getMaxReorderQuantity(),
            'line_weight' => $this->line_weight,
        ]);
    }

    /**
     * Get validation rules for order item creation/update.
     */
    public static function getValidationRules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'book_id' => 'required|exists:books,id',
            'book_title' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0|max:99999999.99',
            'total_price' => 'required|numeric|min:0|max:99999999.99',
        ];
    }
}