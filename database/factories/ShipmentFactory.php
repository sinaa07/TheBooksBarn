<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        return [
            'order_id' => $order->id,
            'tracking_number' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'carrier' => $this->faker->randomElement(['Delhivery', 'BlueDart', 'DTDC', 'Ecom Express']),
            'shipment_status' => $this->faker->randomElement([
                'preparing', 'shipped', 'in_transit', 'delivered'
            ]),
            'notes' => $this->faker->optional()->sentence(),
            'shipped_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'delivered_at' => $this->faker->optional()->dateTimeBetween('now', '+1 week'),
        ];
    }
}