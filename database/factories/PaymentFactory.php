<?php

namespace Database\Factories;
use App\Models\Payment;
use App\Models\Order;
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
            'order_id' => $order->id,
            'mode' => $this->faker->randomElement(['credit_card', 'debit_card', 'net_banking', 'UPI']),
            'status' => $this->faker->randomElement(['success', 'failed', 'pending']),
            'transaction_id' => $this->faker->unique()->uuid,
            'transaction_amt' => $order->total_amt,
            'gateway' => $this->faker->randomElement(['Razorpay', 'Stripe', 'PayPal']),
            'paid_at' => now(),
        ];
    }
}
