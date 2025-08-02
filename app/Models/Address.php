<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'address_type',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'address_type' => 'string',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure only one default address per user
        static::saving(function ($address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    // Relationships

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors

    /**
     * Get the full address as a single string.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->address_line_1;
        
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        
        $address .= ', ' . $this->city . ', ' . $this->state . ' ' . $this->postal_code;
        
        if ($this->country !== 'India') {
            $address .= ', ' . $this->country;
        }
        
        return $address;
    }

    /**
     * Get formatted address for display.
     */
    public function getFormattedAddressAttribute(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'line1' => $this->address_line_1,
            'line2' => $this->address_line_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
        ];
    }

    /**
     * Get address for JSON storage (used in orders).
     */
    public function getAddressForStorageAttribute(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
        ];
    }

    // Scopes

    /**
     * Scope a query to only include default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include shipping addresses.
     */
    public function scopeShipping($query)
    {
        return $query->whereIn('address_type', ['shipping', 'both']);
    }

    /**
     * Scope a query to only include billing addresses.
     */
    public function scopeBilling($query)
    {
        return $query->whereIn('address_type', ['billing', 'both']);
    }

    /**
     * Scope a query to filter by address type.
     */
    public function scopeOfType($query, string $type)
    {
        if (in_array($type, ['billing', 'shipping'])) {
            return $query->whereIn('address_type', [$type, 'both']);
        }
        
        return $query->where('address_type', $type);
    }

    /**
     * Scope a query to filter by country.
     */
    public function scopeInCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    // Helper Methods

    /**
     * Check if this is a shipping address.
     */
    public function isShippingAddress(): bool
    {
        return in_array($this->address_type, ['shipping', 'both']);
    }

    /**
     * Check if this is a billing address.
     */
    public function isBillingAddress(): bool
    {
        return in_array($this->address_type, ['billing', 'both']);
    }

    /**
     * Check if this is the default address.
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Make this address the default for the user.
     */
    public function makeDefault(): bool
    {
        return $this->update(['is_default' => true]);
    }

    /**
     * Remove default status from this address.
     */
    public function removeDefault(): bool
    {
        return $this->update(['is_default' => false]);
    }

    /**
     * Get validation rules for address creation/update.
     */
    public static function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'address_type' => 'required|in:billing,shipping,both',
            'is_default' => 'boolean',
        ];
    }
}