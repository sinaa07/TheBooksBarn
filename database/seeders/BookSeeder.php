<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure categories exist first
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        // Create 9 books for each category
        Category::all()->each(function ($category) {
            Book::factory()->count(10)->create([
                'category_id' => $category->id, // FIXED: correct foreign key name
            ]);
        });
    }
}