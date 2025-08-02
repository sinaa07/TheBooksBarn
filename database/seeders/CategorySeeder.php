<?php

namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Fiction', 'slug' => 'fiction', 'description' => 'Fictional and literary works.'],
            ['category_name' => 'Non-Fiction', 'slug' => 'non-fiction', 'description' => 'Biographies, essays, and real-world topics.'],
            ['category_name' => 'Mystery & Thriller', 'slug' => 'mystery-thriller', 'description' => 'Crime, thrillers, and mystery stories.'],
            ['category_name' => 'Science Fiction & Fantasy', 'slug' => 'sci-fi-fantasy', 'description' => 'Sci-fi, fantasy, and speculative fiction.'],
            ['category_name' => 'Children\'s Books', 'slug' => 'childrens-books', 'description' => 'Books for kids and young readers.'],
        ];

        foreach ($categories as $category) {
            Category::factory()->create($category);
        }
    }
}