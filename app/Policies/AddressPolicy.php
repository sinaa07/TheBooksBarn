<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

use Illuminate\Auth\Access\Response;

class AddressPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Address $address): bool
    {
        return $user->user_id === $address->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Address $address): bool
    {
        return $user->user_id === $address->user_id;
    }

    public function delete(User $user, Address $address): bool
    {
        return $user->user_id === $address->user_id;
    }

    public function restore(User $user, Address $address): bool
    {
        return $user->user_id === $address->user_id;
    }

    public function forceDelete(User $user, Address $address): bool
    {
        return $user->user_id === $address->user_id;
    }
}