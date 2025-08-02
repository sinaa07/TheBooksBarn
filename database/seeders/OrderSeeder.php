<?php

namespace Database\Seeders;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have users
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        // Create 1 order for each user
        User::all()->each(function ($user) {
            Order::factory()->count(1)->create([
                'user_id' => $user->id, // FIXED
            ]);
        });
    }
}
