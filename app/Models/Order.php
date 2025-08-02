<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'order_status',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'shipping_address',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Generate order number when creating
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });

        // Update timestamps when status changes
        static::updating(function ($order) {
            if ($order->isDirty('order_status')) {
                switch ($order->order_status) {
                    case 'shipped':
                        if (!$order->shipped_at) {
                            $order->shipped_at = Carbon::now();
                        }
                        break;
                    case 'delivered':
                        if (!$order->delivered_at) {
                            $order->delivered_at = Carbon::now();
                        }
                        if (!$order->shipped_at) {
                            $order->shipped_at = Carbon::now();
                        }
                        break;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function itemsWithBooks()
    {
        return $this->orderItems()->with(['book']);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '₹' . number_format($this->subtotal, 2);
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return '₹' . number_format($this->shipping_cost, 2);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->orderItems()->sum('quantity');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->order_status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->order_status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'orange',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->order_status, ['pending', 'confirmed']);
    }

    public function getCanBeModifiedAttribute(): bool
    {
        return in_array($this->order_status, ['pending']);
    }

    public function getEstimatedDeliveryAttribute(): ?Carbon
    {
        if ($this->shipped_at) {
            return $this->shipped_at->addDays(3); // 3 days after shipping
        }
        
        if (in_array($this->order_status, ['confirmed', 'processing'])) {
            return $this->created_at->addDays(7); // 7 days from order
        }
        
        return null;
    }

    public function getDaysSinceOrderAttribute(): int
    {
        return $this->created_at->diffInDays(Carbon::now());
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('order_status', $status);
    }


    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('order_status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('order_status', 'processing');
    }

    /**
     * Scope a query to only include shipped orders.
     */
    public function scopeShipped($query)
    {
        return $query->where('order_status', 'shipped');
    }

    /**
     * Scope a query to only include delivered orders.
     */
    public function scopeDelivered($query)
    {
        return $query->where('order_status', 'delivered');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('order_status', 'cancelled');
    }

    /**
     * Scope a query to only include active orders (not cancelled).
     */
    public function scopeActive($query)
    {
        return $query->where('order_status', '!=', 'cancelled');
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('order_status', ['delivered']);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by total amount range.
     */
    public function scopeAmountRange($query, float $minAmount, float $maxAmount)
    {
        return $query->whereBetween('total_amount', [$minAmount, $maxAmount]);
    }

    /**
     * Scope a query to search by order number or customer info.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('order_number', 'LIKE', "%{$term}%")
                  ->orWhereHas('user', function ($userQuery) use ($term) {
                      $userQuery->where('first_name', 'LIKE', "%{$term}%")
                               ->orWhere('last_name', 'LIKE', "%{$term}%")
                               ->orWhere('email', 'LIKE', "%{$term}%");
                  });
        });
    }

    // Helper Methods

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->order_status === 'pending';
    }

    /**
     * Check if the order is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->order_status === 'confirmed';
    }

    /**
     * Check if the order is processing.
     */
    public function isProcessing(): bool
    {
        return $this->order_status === 'processing';
    }

    /**
     * Check if the order is shipped.
     */
    public function isShipped(): bool
    {
        return $this->order_status === 'shipped';
    }

    /**
     * Check if the order is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->order_status === 'delivered';
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->order_status === 'cancelled';
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return $this->can_be_cancelled;
    }

    /**
     * Check if the order can be modified.
     */
    public function canBeModified(): bool
    {
        return $this->can_be_modified;
    }

    /**
     * Confirm the order.
     */
    public function confirm(): bool
    {
        if (!$this->isPending()) {
            return false;
        }
        
        return $this->update(['order_status' => 'confirmed']);
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing(): bool
    {
        if (!in_array($this->order_status, ['pending', 'confirmed'])) {
            return false;
        }
        
        return $this->update(['order_status' => 'processing']);
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped(): bool
    {
        if (!in_array($this->order_status, ['confirmed', 'processing'])) {
            return false;
        }
        
        return $this->update([
            'order_status' => 'shipped',
            'shipped_at' => Carbon::now()
        ]);
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered(): bool
    {
        if (!in_array($this->order_status, ['shipped'])) {
            return false;
        }
        
        return $this->update([
            'order_status' => 'delivered',
            'delivered_at' => Carbon::now()
        ]);
    }

    /**
     * Cancel the order.
     */
    public function cancel(string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }
        
        // Restore stock for all items
        foreach ($this->orderItems as $item) {
            if ($item->book) {
                $item->book->increaseStock($item->quantity);
            }
        }
        
        $updateData = ['order_status' => 'cancelled'];
        if ($reason) {
            $updateData['notes'] = ($this->notes ? $this->notes . "\n" : '') . "Cancelled: " . $reason;
        }
        
        return $this->update($updateData);
    }

    /**
     * Create order from cart.
     */
    public static function createFromCart(Cart $cart, Address $shippingAddress, array $additionalData = []): ?static
    {
        if (!$cart->isValidForCheckout()) {
            return null;
        }
        
        $subtotal = $cart->total_amount;
        $shippingCost = $cart->getEstimatedShipping();
        $totalAmount = $subtotal + $shippingCost;
        
        $order = static::create(array_merge([
            'user_id' => $cart->user_id,
            'order_status' => 'pending',
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_amount' => $totalAmount,
            'shipping_address' => $shippingAddress->address_for_storage,
        ], $additionalData));
        
        // Create order items
        foreach ($cart->cartItems as $cartItem) {
            $order->orderItems()->create($cartItem->toOrderItemData());
            
            // Reduce stock
            if ($cartItem->book) {
                $cartItem->book->decreaseStock($cartItem->quantity);
            }
        }
        
        // Clear cart
        $cart->clear();
        
        return $order;
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = Carbon::now()->format('Ymd');
        $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $orderNumber = $prefix . $timestamp . $randomNumber;
        
        // Ensure uniqueness
        while (static::where('order_number', $orderNumber)->exists()) {
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $orderNumber = $prefix . $timestamp . $randomNumber;
        }
        
        return $orderNumber;
    }

    public static function getValidationRules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'subtotal' => 'required|numeric|min:0|max:99999999.99',
            'shipping_cost' => 'required|numeric|min:0|max:99999999.99',
            'total_amount' => 'required|numeric|min:0|max:99999999.99',
            'shipping_address' => 'required|array',
            'notes' => 'nullable|string',
        ];
    }
}