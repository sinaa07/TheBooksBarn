<?php

namespace Database\Factories;

use App\Models\Category;
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
        $name = $this->faker->unique()->words(2, true); // generates a name like "Science Fiction"

        return [
            'cat_name' => ucfirst($name),
            'slug' => Str::slug($name),
            'image' => $this->faker->optional()->imageUrl(640, 480, 'books', true, 'category'),
        ];
    }
}