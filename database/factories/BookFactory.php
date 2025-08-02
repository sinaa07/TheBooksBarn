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
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => $name,
            'author' => $this->faker->name(),
            'cat_id' => Category::factory(), // references categories table
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => $this->faker->optional()->imageUrl(300, 400, 'books', true),
            'desc' => $this->faker->optional()->paragraph(),
            'publisher' => $this->faker->optional()->company(),
            'published_in' => $this->faker->year(),
            'language' => $this->faker->optional()->randomElement(['English', 'Hindi', 'French', 'German']),
            'slug' => Str::slug($name . '-' . Str::random(5)),
            'ISBN' => $this->faker->unique()->isbn13(),
        ];
    }
}