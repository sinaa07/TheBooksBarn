<?php

namespace Database\Seeders;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        foreach($orders as $order){
            Payment::factory()->count(1)->create([
                'order_id' => $order->order_id,
                'transaction_amt' => $order->total_amt,
            ]);
        }
    }
}
