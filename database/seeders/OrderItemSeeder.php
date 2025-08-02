<?php

namespace Database\Seeders;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have books
        if (Book::count() === 0) {
            $this->call(BookSeeder::class);
        }

        // Ensure we have orders
        if (Order::count() === 0) {
            $this->call(OrderSeeder::class);
        }

        $books = Book::all();

        // Create 1 item per order
        Order::all()->each(function ($order) use ($books) {
            $book = $books->random();

            OrderItem::factory()->count(1)->create([
                'order_id'    => $order->id,  // FIXED
                'book_id'     => $book->id,   // FIXED
                'book_title'  => $book->title, // Fill required field
                'quantity'    => 1,
                'unit_price'  => $book->price,
                'total_price' => $book->price, // qty * unit_price
            ]);
        });
    }
}
