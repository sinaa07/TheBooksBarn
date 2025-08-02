<?php

namespace Database\Seeders;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have users
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        // Create 2 addresses for each user
        User::all()->each(function ($user) {
            Address::factory()->count(2)->create([
                'user_id' => $user->id, // FIXED: correct foreign key
            ]);
        });
    }
}
