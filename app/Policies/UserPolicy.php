<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user)
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        // Users can view their own profile, admins can view any user
        return $user->id === $model->id || $this->isAdmin($user);
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user)
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model)
    {
        // Users can update their own profile, admins can update any user
        return $user->id === $model->id || $this->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        // Only admins can delete users, and they can't delete themselves
        return $this->isAdmin($user) && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model)
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model)
    {
        return $this->isAdmin($user) && $user->id !== $model->id;
    }

    /**
     * Check if the user is an admin
     */
    private function isAdmin(User $user): bool
    {
        return Admin::where('user_id', $user->id)->exists();
    }
}