<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // creates and assigns a new user
            'total_amt' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['confirmed', 'processing', 'completed', 'pending']),
            'ordered_at' => now(),
        ];
    }
}
