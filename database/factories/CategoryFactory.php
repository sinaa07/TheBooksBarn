<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true); // e.g., "Science Fiction"

        return [
            'category_name' => ucfirst($name),
            'description' => $this->faker->optional()->sentence(),
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }
}