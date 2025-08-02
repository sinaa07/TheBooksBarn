<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 1000);
        $shippingCost = $this->faker->randomFloat(2, 20, 100);

        return [
            'user_id' => User::factory(),
            'order_number' => strtoupper(Str::random(10)), // Unique random code
            'order_status' => $this->faker->randomElement([
                'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'
            ]),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_amount' => $subtotal + $shippingCost,
            'shipping_address' => [
                'name' => $this->faker->name(),
                'phone' => $this->faker->phoneNumber(),
                'address_line_1' => $this->faker->streetAddress(),
                'address_line_2' => $this->faker->optional()->secondaryAddress(),
                'city' => $this->faker->city(),
                'state' => $this->faker->state(),
                'postal_code' => $this->faker->postcode(),
                'country' => 'India',
            ],
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}