<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Database\Eloquent\Collection;

class CategoryController extends BaseBookController
{
    public function index(Request $request): Response
    {
        $categories = Category::withCount(['books' => function ($query) {
                $query->where('is_active', true)
                      ->where('stock_quantity', '>', 0);
            }])
            ->where('is_active', true)
            ->having('books_count', '>', 0)
            ->orderBy('category_name')
            ->get();

        $categories->transform(function ($category) {
            return [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'slug' => $category->slug,
                'description' => $category->description,
                'books_count' => $category->books_count,
            ];
        });

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $sortBy = $request->get('sort_by', 'newest');

        $booksQuery = Book::with('category')
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        // Apply shared filtering and sorting
        $booksQuery = $this->applyBookFilters($booksQuery, $request);
        $booksQuery = $this->applyBookSorting($booksQuery, $sortBy);

        $books = $booksQuery->paginate(12)->appends($request->query());
        $this->transformBookCollection($books);

        // Get filter data for this category
        $formats = $this->getBookFormats($category->id);
        $priceRange = $this->getBookPriceRange($category->id);

        return Inertia::render('Categories/Show', [
            'category' => [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'description' => $category->description,
                'slug' => $category->slug,
            ],
            'books' => $books,
            'formats' => $formats,
            'priceRange' => $priceRange,
            'filters' => [
                'sort_by' => $sortBy,
                'format' => $request->get('format'),
                'min_price' => $request->get('min_price'),
                'max_price' => $request->get('max_price'),
            ],
        ]);
    }

    public function popular(Request $request): Response
    {
        $categories = Category::withCount(['books' => function ($query) {
                $query->where('is_active', true)
                      ->where('stock_quantity', '>', 0);
            }])
            ->where('is_active', true)
            ->having('books_count', '>', 0)
            ->orderBy('books_count', 'desc')
            ->paginate(12);

        $categories->getCollection()->transform(function ($category) {
            return [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'slug' => $category->slug,
                'description' => $category->description,
                'books_count' => $category->books_count,
            ];
        });

        return Inertia::render('Categories/Popular', [
            'categories' => $categories,
        ]);
    }
}