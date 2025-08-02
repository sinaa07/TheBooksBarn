<?php

namespace Database\Seeders;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        $orders = Order::all();
        foreach($orders as $order){
            OrderItem::factory()->count(1)->create([
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'book_id' => $books->random()->book_id,
            ]);
        }
    }
}
