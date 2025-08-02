<?php

namespace Database\Factories;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        return [
            'order_id' => $order->id, // matches migration FK
            'payment_method' => $this->faker->randomElement([
                'credit_card', 'debit_card', 'paypal', 'cash_on_delivery'
            ]),
            'payment_status' => $this->faker->randomElement([
                'pending', 'completed', 'failed', 'refunded'
            ]),
            'amount' => $order->total_amount ?? $this->faker->randomFloat(2, 100, 1000),
            'transaction_id' => $this->faker->unique()->uuid(),
            'notes' => $this->faker->optional()->sentence(),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
