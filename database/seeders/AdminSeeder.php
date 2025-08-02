<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get users who don't already have an admin record
        $eligibleUsers = User::whereDoesntHave('admin')->get();

        // Limit to avoid exceeding available unique users
        $count = min(10, $eligibleUsers->count());

        // Assign admin role only to eligible users
        foreach ($eligibleUsers->take($count) as $user) {
            Admin::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}