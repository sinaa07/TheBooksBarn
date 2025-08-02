<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'book_id',
        'quantity',
        'unit_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Validate quantity before saving
        static::saving(function ($cartItem) {
            if ($cartItem->quantity <= 0) {
                return false; // Prevent saving invalid quantities
            }
            
            // Update unit price to current book price if it's different
            if ($cartItem->book && $cartItem->unit_price != $cartItem->book->price) {
                $cartItem->unit_price = $cartItem->book->price;
            }
        });
    }

    // Relationships

    /**
     * Get the cart that owns the cart item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the book associated with the cart item.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Accessors

    /**
     * Get the total price for this cart item.
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get the formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '₹' . number_format($this->total_price, 2);
    }

    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '₹' . number_format($this->unit_price, 2);
    }

    /**
     * Check if the item is available (book is active and in stock).
     */
    public function getIsAvailableAttribute(): bool
    {
        if (!$this->book) {
            return false;
        }
        
        return $this->book->is_active && $this->book->stock_quantity >= $this->quantity;
    }

    /**
     * Check if there's sufficient stock for this item.
     */
    public function getHasSufficientStockAttribute(): bool
    {
        if (!$this->book) {
            return false;
        }
        
        return $this->book->stock_quantity >= $this->quantity;
    }

    /**
     * Get the maximum quantity available for this item.
     */
    public function getMaxQuantityAttribute(): int
    {
        if (!$this->book) {
            return 0;
        }
        
        return $this->book->stock_quantity;
    }

    /**
     * Get stock status for this item.
     */
    public function getStockStatusAttribute(): string
    {
        if (!$this->book) {
            return 'unavailable';
        }
        
        if (!$this->book->is_active) {
            return 'discontinued';
        }
        
        if ($this->book->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        
        if ($this->book->stock_quantity < $this->quantity) {
            return 'insufficient_stock';
        }
        
        return 'available';
    }

    /**
     * Check if price has changed since item was added.
     */
    public function getHasPriceChangedAttribute(): bool
    {
        if (!$this->book) {
            return false;
        }
        
        return $this->unit_price != $this->book->price;
    }

    /**
     * Get price difference if price has changed.
     */
    public function getPriceDifferenceAttribute(): float
    {
        if (!$this->book || !$this->has_price_changed) {
            return 0.0;
        }
        
        return $this->book->price - $this->unit_price;
    }

    // Scopes

    /**
     * Scope a query to only include available items.
     */
    public function scopeAvailable($query)
    {
        return $query->whereHas('book', function ($query) {
            $query->where('is_active', true)
                  ->where('stock_quantity', '>', 0);
        });
    }

    /**
     * Scope a query to only include unavailable items.
     */
    public function scopeUnavailable($query)
    {
        return $query->whereHas('book', function ($query) {
            $query->where('is_active', false)
                  ->orWhere('stock_quantity', '<=', 0);
        });
    }

    /**
     * Scope a query to only include items with insufficient stock.
     */
    public function scopeInsufficientStock($query)
    {
        return $query->whereHas('book', function ($bookQuery) {
            $bookQuery->whereColumn('stock_quantity', '<', 'cart_items.quantity');
        });
    }

    /**
     * Scope a query to only include items with price changes.
     */
    public function scopePriceChanged($query)
    {
        return $query->whereHas('book', function ($bookQuery) {
            $bookQuery->whereColumn('price', '!=', 'cart_items.unit_price');
        });
    }

    // Helper Methods

    /**
     * Check if the item is available for purchase.
     */
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    /**
     * Check if there's sufficient stock.
     */
    public function hasSufficientStock(): bool
    {
        return $this->has_sufficient_stock;
    }

    /**
     * Check if price has changed.
     */
    public function hasPriceChanged(): bool
    {
        return $this->has_price_changed;
    }

    /**
     * Update quantity with validation.
     */
    public function updateQuantity(int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->delete();
        }
        
        if (!$this->book || $this->book->stock_quantity < $quantity) {
            return false;
        }
        
        return $this->update(['quantity' => $quantity]);
    }

    /**
     * Increase quantity by specified amount.
     */
    public function increaseQuantity(int $amount = 1): bool
    {
        return $this->updateQuantity($this->quantity + $amount);
    }

    /**
     * Decrease quantity by specified amount.
     */
    public function decreaseQuantity(int $amount = 1): bool
    {
        return $this->updateQuantity($this->quantity - $amount);
    }

    /**
     * Update unit price to current book price.
     */
    public function syncPrice(): bool
    {
        if (!$this->book) {
            return false;
        }
        
        return $this->update(['unit_price' => $this->book->price]);
    }

    /**
     * Fix quantity to match available stock.
     */
    public function fixQuantity(): bool
    {
        if (!$this->book) {
            return $this->delete();
        }
        
        if ($this->book->stock_quantity <= 0) {
            return $this->delete();
        }
        
        if ($this->quantity > $this->book->stock_quantity) {
            return $this->update(['quantity' => $this->book->stock_quantity]);
        }
        
        return true; // No fix needed
    }

    /**
     * Get savings amount if there's a price decrease.
     */
    public function getSavings(): float
    {
        if (!$this->has_price_changed || $this->price_difference >= 0) {
            return 0.0;
        }
        
        return abs($this->price_difference) * $this->quantity;
    }

    /**
     * Get additional cost if there's a price increase.
     */
    public function getAdditionalCost(): float
    {
        if (!$this->has_price_changed || $this->price_difference <= 0) {
            return 0.0;
        }
        
        return $this->price_difference * $this->quantity;
    }

    /**
     * Convert to order item data.
     */
    public function toOrderItemData(): array
    {
        return [
            'book_id' => $this->book_id,
            'book_title' => $this->book?->title ?? 'Unknown Book',
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
        ];
    }

    /**
     * Get item data for API/frontend.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'total_price' => $this->total_price,
            'formatted_total_price' => $this->formatted_total_price,
            'formatted_unit_price' => $this->formatted_unit_price,
            'is_available' => $this->is_available,
            'has_sufficient_stock' => $this->has_sufficient_stock,
            'max_quantity' => $this->max_quantity,
            'stock_status' => $this->stock_status,
            'has_price_changed' => $this->has_price_changed,
            'price_difference' => $this->price_difference,
            'savings' => $this->getSavings(),
            'additional_cost' => $this->getAdditionalCost(),
        ]);
    }

    /**
     * Get validation rules for cart item creation/update.
     */
    public static function getValidationRules(): array
    {
        return [
            'cart_id' => 'required|exists:carts,id',
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0|max:99999999.99',
        ];
    }
}