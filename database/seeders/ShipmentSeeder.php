<?php

namespace Database\Seeders;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $processingOrders = Order::where('status','processing')->get();
        
        foreach($processingOrders as $order){
            Shipment::factory()->count(1)->create([
                'order_id' => $order->order_id,
            ]);
        }
    }
}
