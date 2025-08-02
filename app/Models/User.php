<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Automatically create a cart when user is created
        static::created(function ($user) {
            $user->cart()->create();
        });
    }

    // Relationships

    /**
     * Get all addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the user's default shipping address.
     */
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    /**
     * Get shipping addresses for the user.
     */
    public function shippingAddresses()
    {
        return $this->hasMany(Address::class)->whereIn('address_type', ['shipping', 'both']);
    }

    /**
     * Get billing addresses for the user.
     */
    public function billingAddresses()
    {
        return $this->hasMany(Address::class)->whereIn('address_type', ['billing', 'both']);
    }

    /**
     * Get the user's admin record if they are an admin.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Get the user's cart.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get all orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Accessors & Mutators

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // Scopes

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Helper Methods

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->admin()->exists();
    }

    /**
     * Check if the user is a manager.
     */
    public function isManager()
    {
        return $this->admin()->where('role', 'manager')->exists();
    }

    /**
     * Check if the user has admin privileges.
     */
    public function hasAdminRole()
    {
        return $this->admin()->where('role', 'admin')->exists();
    }

    /**
     * Get the total number of items in the user's cart.
     */
    public function getCartItemsCount()
    {
        return $this->cart?->cartItems()->sum('quantity') ?? 0;
    }

    /**
     * Get the total value of items in the user's cart.
     */
    public function getCartTotal()
    {
        return $this->cart?->cartItems()
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;
    }

    /**
     * Get the user's recent orders.
     */
    public function getRecentOrders($limit = 5)
    {
        return $this->orders()
            ->with(['orderItems.book'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Deactivate the user account.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate the user account.
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
    }
}