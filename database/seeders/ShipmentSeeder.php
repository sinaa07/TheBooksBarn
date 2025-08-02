<?php

namespace Database\Seeders;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all orders that are processing
        $processingOrders = Order::where('order_status', 'processing')->get(); // FIXED

        // Create 1 shipment for each processing order
        $processingOrders->each(function ($order) {
            Shipment::factory()->count(1)->create([
                'order_id' => $order->id, // FIXED
            ]);
        });
    }
}