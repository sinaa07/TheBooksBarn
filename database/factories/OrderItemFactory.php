<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();
        $book = Book::inRandomOrder()->first() ?? Book::factory()->create();

        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $book->price;
        $totalPrice = $unitPrice * $quantity;

        return [
            'order_id' => $order->id, // from migration
            'book_id' => $book->id,   // from migration
            'book_title' => $book->title, // store book title for historical record
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
        ];
    }
}