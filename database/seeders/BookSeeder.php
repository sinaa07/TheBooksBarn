<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        if($categories->isempty()){
            $catgeory = Category::factory()->count(5)->create();
        }
        foreach($categories as $category){
            Book::factory()->count(9)->create([
                'cat_id' => $category->cat_id,
            ]);
        }

    }
}
