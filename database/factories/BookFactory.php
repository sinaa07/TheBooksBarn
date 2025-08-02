<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'isbn' => $this->faker->unique()->isbn13(),
            'title' => $title,
            'author' => $this->faker->name(),
            'category_id' => Category::factory(), // Correct foreign key
            'description' => $this->faker->optional()->paragraph(),
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'format' => $this->faker->randomElement(['hardcover', 'paperback', 'ebook']),
            'cover_image_url' => $this->faker->optional()->imageUrl(300, 400, 'books', true),
            'is_active' => true,
            'featured' => $this->faker->boolean(20), // 20% chance of being featured
        ];
    }
}