<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_status',
        'amount',
        'transaction_id',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::updating(function ($payment) {
            if ($payment->isDirty('payment_status') && $payment->payment_status === 'completed') {
                if (!$payment->completed_at) {
                    $payment->completed_at = Carbon::now();
                }
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'yellow',
            'completed' => 'green',
            'failed' => 'red',
            'refunded' => 'orange',
            default => 'gray'
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'paypal' => 'PayPal',
            'cash_on_delivery' => 'Cash on Delivery',
            default => 'Unknown'
        };
    }

    public function getCanBeRefundedAttribute(): bool
    {
        return $this->payment_status === 'completed' && 
               $this->payment_method !== 'cash_on_delivery';
    }

    public function getIsDigitalPaymentAttribute(): bool
    {
        return in_array($this->payment_method, ['credit_card', 'debit_card', 'paypal']);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'refunded');
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeDigitalPayments($query)
    {
        return $query->whereIn('payment_method', ['credit_card', 'debit_card', 'paypal']);
    }

    public function scopeCashOnDelivery($query)
    {
        return $query->where('payment_method', 'cash_on_delivery');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeAmountRange($query, float $minAmount, float $maxAmount)
    {
        return $query->whereBetween('amount', [$minAmount, $maxAmount]);
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    public function canBeRefunded(): bool
    {
        return $this->can_be_refunded;
    }

    public function isDigitalPayment(): bool
    {
        return $this->is_digital_payment;
    }

    public function markAsCompleted(string $transactionId = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $updateData = [
            'payment_status' => 'completed',
            'completed_at' => Carbon::now()
        ];

        if ($transactionId) {
            $updateData['transaction_id'] = $transactionId;
        }

        return $this->update($updateData);
    }

    public function markAsFailed(string $reason = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $updateData = ['payment_status' => 'failed'];

        if ($reason) {
            $updateData['notes'] = ($this->notes ? $this->notes . "\n" : '') . "Failed: " . $reason;
        }

        return $this->update($updateData);
    }

    public function refund(float $amount = null, string $reason = null): bool
    {
        if (!$this->canBeRefunded()) {
            return false;
        }

        $refundAmount = $amount ?? $this->amount;

        if ($refundAmount > $this->amount) {
            return false;
        }

        $updateData = ['payment_status' => 'refunded'];

        if ($reason) {
            $updateData['notes'] = ($this->notes ? $this->notes . "\n" : '') . 
                                  "Refunded ₹{$refundAmount}: " . $reason;
        }

        return $this->update($updateData);
    }

    public function retry(): bool
    {
        if (!$this->isFailed()) {
            return false;
        }

        return $this->update([
            'payment_status' => 'pending',
            'notes' => ($this->notes ? $this->notes . "\n" : '') . "Payment retried at " . Carbon::now()
        ]);
    }

    public function getDaysSincePaymentAttribute(): int
    {
        return $this->created_at->diffInDays(Carbon::now());
    }

    public function getProcessingTimeAttribute(): ?int
    {
        if (!$this->completed_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->completed_at);
    }

    public static function getTotalRevenue($startDate = null, $endDate = null): float
    {
        $query = static::completed();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->sum('amount') ?? 0.0;
    }

    public static function getPaymentMethodStats($startDate = null, $endDate = null): array
    {
        $query = static::completed();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                    ->groupBy('payment_method')
                    ->get()
                    ->keyBy('payment_method')
                    ->toArray();
    }

    public static function getFailureRate($startDate = null, $endDate = null): float
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $total = $query->count();
        $failed = $query->failed()->count();

        return $total > 0 ? ($failed / $total) * 100 : 0;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'formatted_amount' => $this->formatted_amount,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'method_label' => $this->method_label,
            'can_be_refunded' => $this->can_be_refunded,
            'is_digital_payment' => $this->is_digital_payment,
            'days_since_payment' => $this->days_since_payment,
            'processing_time' => $this->processing_time,
        ]);
    }

    public static function getValidationRules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:credit_card,debit_card,paypal,cash_on_delivery',
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'amount' => 'required|numeric|min:0|max:99999999.99',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}