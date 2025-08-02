<?php

namespace Database\Seeders;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have orders to work with
        if (Order::count() === 0) {
            $this->call(OrderSeeder::class);
        }

        // Create one payment per order
        Order::all()->each(function ($order) {
            Payment::factory()->count(1)->create([
                'order_id' => $order->id,          // FIXED
                'amount'   => $order->total_amount // FIXED
            ]);
        });
    }
}