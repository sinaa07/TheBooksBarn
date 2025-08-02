<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        return [
            'order_id' => $order->id,
            'courier' => $this->faker->randomElement(['Delhivery', 'BlueDart', 'DTDC', 'Ecom Express']),
            'tracking_id' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'status' => $this->faker->randomElement(['pending', 'shipped', 'in_transit', 'delivered']),
        ];
    }
}