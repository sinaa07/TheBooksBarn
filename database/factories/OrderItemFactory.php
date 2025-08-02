<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $order = Order::inRandomOrder()->where('user_id', $user->user_id)->first() ?? Order::factory()->create(['user_id' => $user->user_id]);
        $book = Book::inRandomOrder()->first() ?? Book::factory()->create();

        $quantity = $this->faker->numberBetween(1, 5);
        $amount = $book->price * $quantity;

        return [
            'order_id' => $order->order_id,
            'user_id' => $user->user_id,
            'book_id' => $book->book_id,
            'quantity' => $quantity,
            'amount' => $amount,
        ];
    }
}