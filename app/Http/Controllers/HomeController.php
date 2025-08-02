<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $featuredBooks = Book::with('category')
            ->where('is_active', true)
            ->where('featured', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'price' => $book->price,
                    'cover_image_url' => $book->cover_image_url,
                    'category' => $book->category?->category_name,
                    'format' => $book->format,
                    'stock_quantity' => $book->stock_quantity,
                ];
            });

        $latestBooks = Book::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'price' => $book->price,
                    'cover_image_url' => $book->cover_image_url,
                    'category' => $book->category?->category_name,
                    'format' => $book->format,
                    'stock_quantity' => $book->stock_quantity,
                ];
            });

        $popularCategories = Category::withCount(['books' => function ($query) {
                $query->where('is_active', true)
                      ->where('stock_quantity', '>', 0);
            }])
            ->where('is_active', true)
            ->having('books_count', '>', 0)
            ->orderBy('books_count', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'books_count' => $category->books_count,
                ];
            });

        $bestsellers = Book::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->limit(6)
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'price' => $book->price,
                    'cover_image_url' => $book->cover_image_url,
                    'category' => $book->category?->category_name,
                    'format' => $book->format,
                    'stock_quantity' => $book->stock_quantity,
                ];
            });

        $stats = [
            'total_books' => Book::where('is_active', true)->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'books_in_stock' => Book::where('is_active', true)
                                   ->where('stock_quantity', '>', 0)
                                   ->count(),
        ];

        return Inertia::render('Home/Index', [
            'featuredBooks' => $featuredBooks,
            'latestBooks' => $latestBooks,
            'popularCategories' => $popularCategories,
            'bestsellers' => $bestsellers,
            'stats' => $stats,
        ]);
    }
}