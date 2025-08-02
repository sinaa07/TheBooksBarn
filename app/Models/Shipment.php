<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'shipment_status',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::updating(function ($shipment) {
            if ($shipment->isDirty('shipment_status')) {
                switch ($shipment->shipment_status) {
                    case 'shipped':
                        if (!$shipment->shipped_at) {
                            $shipment->shipped_at = Carbon::now();
                        }
                        break;
                    case 'delivered':
                        if (!$shipment->delivered_at) {
                            $shipment->delivered_at = Carbon::now();
                        }
                        if (!$shipment->shipped_at) {
                            $shipment->shipped_at = Carbon::now();
                        }
                        break;
                }
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->shipment_status) {
            'preparing' => 'Preparing',
            'shipped' => 'Shipped',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->shipment_status) {
            'preparing' => 'yellow',
            'shipped' => 'blue',
            'in_transit' => 'purple',
            'delivered' => 'green',
            default => 'gray'
        };
    }

    public function getCarrierLabelAttribute(): string
    {
        return match($this->carrier) {
            'ups' => 'UPS',
            'fedex' => 'FedEx',
            'dhl' => 'DHL',
            'local' => 'Local Delivery',
            'india_post' => 'India Post',
            'bluedart' => 'Blue Dart',
            'dtdc' => 'DTDC',
            default => $this->carrier ? ucfirst($this->carrier) : 'Unknown'
        };
    }

    public function getEstimatedDeliveryAttribute(): ?Carbon
    {
        if ($this->delivered_at) {
            return $this->delivered_at;
        }

        if ($this->shipped_at) {
            return match($this->carrier) {
                'ups', 'fedex', 'dhl' => $this->shipped_at->addDays(2),
                'bluedart', 'dtdc' => $this->shipped_at->addDays(3),
                'india_post' => $this->shipped_at->addDays(7),
                'local' => $this->shipped_at->addDay(),
                default => $this->shipped_at->addDays(5)
            };
        }

        return $this->created_at->addDays(7);
    }

    public function getDaysInTransitAttribute(): ?int
    {
        if (!$this->shipped_at) {
            return null;
        }

        $endDate = $this->delivered_at ?? Carbon::now();
        return $this->shipped_at->diffInDays($endDate);
    }

    public function getIsDelayedAttribute(): bool
    {
        if ($this->delivered_at || !$this->shipped_at) {
            return false;
        }

        return Carbon::now()->gt($this->estimated_delivery);
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (!$this->tracking_number) {
            return null;
        }

        return match($this->carrier) {
            'ups' => "https://www.ups.com/track?tracknum={$this->tracking_number}",
            'fedex' => "https://www.fedex.com/apps/fedextrack/?tracknumbers={$this->tracking_number}",
            'dhl' => "https://www.dhl.com/in-en/home/tracking.html?tracking-id={$this->tracking_number}",
            'bluedart' => "https://www.bluedart.com/web/guest/trackdartresult?trackFor=0&trackNo={$this->tracking_number}",
            'dtdc' => "https://www.dtdc.in/tracking/consignment_no={$this->tracking_number}",
            default => null
        };
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('shipment_status', $status);
    }

    public function scopePreparing($query)
    {
        return $query->where('shipment_status', 'preparing');
    }

    public function scopeShipped($query)
    {
        return $query->where('shipment_status', 'shipped');
    }

    public function scopeInTransit($query)
    {
        return $query->where('shipment_status', 'in_transit');
    }

    public function scopeDelivered($query)
    {
        return $query->where('shipment_status', 'delivered');
    }

    public function scopeByCarrier($query, string $carrier)
    {
        return $query->where('carrier', $carrier);
    }

    public function scopeDelayed($query)
    {
        return $query->where('shipment_status', '!=', 'delivered')
                    ->whereNotNull('shipped_at')
                    ->where('shipped_at', '<', Carbon::now()->subDays(5));
    }

    public function scopeWithTracking($query)
    {
        return $query->whereNotNull('tracking_number');
    }

    public function scopeWithoutTracking($query)
    {
        return $query->whereNull('tracking_number');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function isPreparing(): bool
    {
        return $this->shipment_status === 'preparing';
    }

    public function isShipped(): bool
    {
        return $this->shipment_status === 'shipped';
    }

    public function isInTransit(): bool
    {
        return $this->shipment_status === 'in_transit';
    }

    public function isDelivered(): bool
    {
        return $this->shipment_status === 'delivered';
    }

    public function isDelayed(): bool
    {
        return $this->is_delayed;
    }

    public function hasTracking(): bool
    {
        return !empty($this->tracking_number);
    }

    public function markAsShipped(string $trackingNumber = null, string $carrier = null): bool
    {
        if (!$this->isPreparing()) {
            return false;
        }

        $updateData = [
            'shipment_status' => 'shipped',
            'shipped_at' => Carbon::now()
        ];

        if ($trackingNumber) {
            $updateData['tracking_number'] = $trackingNumber;
        }

        if ($carrier) {
            $updateData['carrier'] = $carrier;
        }

        $result = $this->update($updateData);

        if ($result) {
            $this->order->markAsShipped();
        }

        return $result;
    }

    public function markAsInTransit(): bool
    {
        if (!$this->isShipped()) {
            return false;
        }

        return $this->update(['shipment_status' => 'in_transit']);
    }

    public function markAsDelivered(): bool
    {
        if (!in_array($this->shipment_status, ['shipped', 'in_transit'])) {
            return false;
        }

        $result = $this->update([
            'shipment_status' => 'delivered',
            'delivered_at' => Carbon::now()
        ]);

        if ($result) {
            $this->order->markAsDelivered();
        }

        return $result;
    }

    public function updateTracking(string $trackingNumber, string $carrier = null): bool
    {
        $updateData = ['tracking_number' => $trackingNumber];

        if ($carrier) {
            $updateData['carrier'] = $carrier;
        }

        return $this->update($updateData);
    }

    public function addNote(string $note): bool
    {
        $existingNotes = $this->notes ? $this->notes . "\n" : '';
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        
        return $this->update([
            'notes' => $existingNotes . "[{$timestamp}] {$note}"
        ]);
    }

    public static function generateTrackingNumber(): string
    {
        $prefix = 'TRK';
        $timestamp = Carbon::now()->format('Ymd');
        $randomNumber = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        
        $trackingNumber = $prefix . $timestamp . $randomNumber;
        
        while (static::where('tracking_number', $trackingNumber)->exists()) {
            $randomNumber = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $trackingNumber = $prefix . $timestamp . $randomNumber;
        }
        
        return $trackingNumber;
    }

    public static function getCarrierOptions(): array
    {
        return [
            'local' => 'Local Delivery',
            'india_post' => 'India Post',
            'bluedart' => 'Blue Dart',
            'dtdc' => 'DTDC',
            'ups' => 'UPS',
            'fedex' => 'FedEx',
            'dhl' => 'DHL',
        ];
    }

    public static function getDeliveryStats($startDate = null, $endDate = null): array
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $total = $query->count();
        $delivered = $query->delivered()->count();
        $delayed = $query->delayed()->count();

        return [
            'total_shipments' => $total,
            'delivered' => $delivered,
            'delayed' => $delayed,
            'delivery_rate' => $total > 0 ? ($delivered / $total) * 100 : 0,
            'delay_rate' => $total > 0 ? ($delayed / $total) * 100 : 0,
        ];
    }

    public static function getAverageDeliveryTime($carrier = null): float
    {
        $query = static::delivered()->whereNotNull('shipped_at');

        if ($carrier) {
            $query->where('carrier', $carrier);
        }

        $shipments = $query->get();

        if ($shipments->isEmpty()) {
            return 0;
        }

        $totalDays = $shipments->sum(function ($shipment) {
            return $shipment->shipped_at->diffInDays($shipment->delivered_at);
        });

        return $totalDays / $shipments->count();
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'carrier_label' => $this->carrier_label,
            'estimated_delivery' => $this->estimated_delivery,
            'days_in_transit' => $this->days_in_transit,
            'is_delayed' => $this->is_delayed,
            'tracking_url' => $this->tracking_url,
            'has_tracking' => $this->hasTracking(),
        ]);
    }

    public static function getValidationRules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'tracking_number' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:50',
            'shipment_status' => 'required|in:preparing,shipped,in_transit,delivered',
            'notes' => 'nullable|string',
        ];
    }
}