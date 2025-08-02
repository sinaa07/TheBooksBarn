<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
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
        $format = $request->get('format');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $booksQuery = Book::with('category')
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        if ($format) {
            $booksQuery->where('format', $format);
        }

        if ($minPrice) {
            $booksQuery->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $booksQuery->where('price', '<=', $maxPrice);
        }

        switch ($sortBy) {
            case 'price_asc':
                $booksQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $booksQuery->orderBy('price', 'desc');
                break;
            case 'title':
                $booksQuery->orderBy('title', 'asc');
                break;
            case 'oldest':
                $booksQuery->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $booksQuery->orderBy('created_at', 'desc');
                break;
        }

        $books = $booksQuery->paginate(12)->appends($request->query());

        $books->getCollection()->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->price,
                'cover_image_url' => $book->cover_image_url,
                'format' => $book->format,
                'stock_quantity' => $book->stock_quantity,
                'description' => $book->description,
            ];
        });

        $formats = Book::select('format')
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->distinct()
            ->orderBy('format')
            ->pluck('format');

        $priceRange = Book::where('category_id', $category->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

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
                'format' => $format,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
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