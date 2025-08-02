<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        // Pick a user who is not already an admin
        $user = User::whereDoesntHave('admin')->inRandomOrder()->first()
            ?? User::factory()->create();

        return [
            'user_id' => $user->id,
            'role' => $this->faker->randomElement(['admin', 'manager']),
        ];
    }
}