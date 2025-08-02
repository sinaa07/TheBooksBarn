<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Available admin roles.
     */
    public const ROLES = [
        'admin' => 'admin',
        'manager' => 'manager',
    ];

    /**
     * Get the user that owns the admin record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the admin has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the admin is an admin role.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLES['admin']);
    }

    /**
     * Check if the admin is a manager role.
     */
    public function isManager(): bool
    {
        return $this->hasRole(self::ROLES['manager']);
    }

    /**
     * Get the admin's full name from the related user.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user ? "{$this->user->first_name} {$this->user->last_name}" : '';
    }

    /**
     * Get the admin's email from the related user.
     */
    public function getEmailAttribute(): string
    {
        return $this->user?->email ?? '';
    }

    /**
     * Get the admin's username from the related user.
     */
    public function getUsernameAttribute(): string
    {
        return $this->user?->username ?? '';
    }

    /**
     * Scope a query to only include admins with admin role.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLES['admin']);
    }

    /**
     * Scope a query to only include admins with manager role.
     */
    public function scopeManagers($query)
    {
        return $query->where('role', self::ROLES['manager']);
    }

    /**
     * Scope a query to include the related user data.
     */
    public function scopeWithUser($query)
    {
        return $query->with('user');
    }

    /**
     * Check if the admin can perform administrative actions.
     * Admins have full access, managers have limited access.
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if the admin can manage books.
     * Both admins and managers can manage books.
     */
    public function canManageBooks(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    /**
     * Check if the admin can manage orders.
     * Both admins and managers can manage orders.
     */
    public function canManageOrders(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    /**
     * Check if the admin can manage categories.
     * Both admins and managers can manage categories.
     */
    public function canManageCategories(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    /**
     * Check if the admin can view reports.
     * Both admins and managers can view reports.
     */
    public function canViewReports(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }
}