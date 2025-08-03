<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    public function index(Request $request): Response
    {
        $booksQuery = Book::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        $sortBy = $request->get('sort_by', 'newest');
        
        switch ($sortBy) {
            case 'price_asc':
                $booksQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $booksQuery->orderBy('price', 'desc');
                break;
            case 'oldest':
                $booksQuery->orderBy('created_at', 'asc');
                break;
            case 'title':
                $booksQuery->orderBy('title', 'asc');
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
                'category' => $book->category?->category_name,
                'category_id' => $book->category_id,
                'format' => $book->format,
                'stock_quantity' => $book->stock_quantity,
                'description' => $book->description,
            ];
        });

        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => [
                'sort_by' => $sortBy,
            ],
        ]);
    }

    public function show(Request $request, int $id): Response
{
    $book = Book::with('category')
        ->where('id', $id)
        ->where('is_active', true)
        ->firstOrFail();

    $relatedBooks = Book::with('category')
        ->where('category_id', $book->category_id)
        ->where('id', '!=', $book->id)
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0)
        ->limit(6)
        ->get()
        ->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->price,
                'cover_image_url' => $book->cover_image_url,
                'format' => $book->format,
            ];
        });

    return Inertia::render('Books/Show', [
        'book' => [
            'id' => $book->id,
            'isbn' => $book->isbn,
            'title' => $book->title,
            'author' => $book->author,
            'description' => $book->description,
            'price' => $book->price,
            'stock_quantity' => $book->stock_quantity,
            'format' => $book->format,
            'cover_image_url' => $book->cover_image_url,
            'category' => $book->category ? [
                'id' => $book->category->id,
                'category_name' => $book->category->category_name,
                'slug' => $book->category->slug,
            ] : null,
            'category_id' => $book->category_id,
        ],
        'relatedBooks' => $relatedBooks,
    ]);
}  

public function search(Request $request): Response
{
    $query = $request->get('q', '');
    $categoryId = $request->get('category_id');
    $format = $request->get('format');
    $minPrice = $request->get('min_price');
    $maxPrice = $request->get('max_price');
    $sortBy = $request->get('sort_by', 'relevance');
    
    $booksQuery = Book::with('category')
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0);
    
    if (!empty($query)) {
        $booksQuery->where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('author', 'like', "%{$query}%")
              ->orWhereHas('category', function($categoryQuery) use ($query) {
                  $categoryQuery->where('category_name', 'like', "%{$query}%");
              });
        });
    }
    
    if ($categoryId) {
        $booksQuery->where('category_id', $categoryId);
    }
    
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
        case 'newest':
            $booksQuery->orderBy('created_at', 'desc');
            break;
        case 'oldest':
            $booksQuery->orderBy('created_at', 'asc');
            break;
        case 'relevance':
        default:
            if (!empty($query)) {
                // Order by most relevant matches first
                $booksQuery->orderByRaw("
                    CASE 
                        WHEN title LIKE ? THEN 1
                        WHEN author LIKE ? THEN 2
                        ELSE 3
                    END ASC
                ", ["%{$query}%", "%{$query}%"]);
            } else {
                $booksQuery->orderBy('created_at', 'desc');
            }
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
            'category' => $book->category?->category_name,
            'category_id' => $book->category_id,
            'format' => $book->format,
            'stock_quantity' => $book->stock_quantity,
            'description' => $book->description,
        ];
    });
    
    $categories = Category::where('is_active', true)
        ->orderBy('category_name')
        ->get(['id', 'category_name']);
    
    $formats = Book::select('format')
        ->where('is_active', true)
        ->distinct()
        ->orderBy('format')
        ->pluck('format');
    
    $priceRange = Book::where('is_active', true)
        ->where('stock_quantity', '>', 0)
        ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
        ->first();
        if (!$priceRange) {
            $priceRange = (object) ['min_price' => 0, 'max_price' => 0];
        } 

    
    return Inertia::render('Books/Search', [
        'books' => $books,
        'categories' => $categories,
        'formats' => $formats,
        'priceRange' => $priceRange,
        'filters' => [
            'q' => $query,
            'category_id' => $categoryId,
            'format' => $format,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort_by' => $sortBy,
        ],
    ]);
}

    public function featured(Request $request): Response
    {
        $books = Book::with('category')
            ->where('is_active', true)
            ->where('featured', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $books->getCollection()->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->price,
                'cover_image_url' => $book->cover_image_url,
                'category' => $book->category?->category_name,
                'format' => $book->format,
                'stock_quantity' => $book->stock_quantity,
                'description' => $book->description,
            ];
        });

        return Inertia::render('Books/Featured', [
            'books' => $books,
        ]);
    }

    public function bestsellers(Request $request): Response
    {
        $books = Book::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->paginate(12);

        $books->getCollection()->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->price,
                'cover_image_url' => $book->cover_image_url,
                'category' => $book->category?->category_name,
                'format' => $book->format,
                'stock_quantity' => $book->stock_quantity,
                'description' => $book->description,
            ];
        });

        return Inertia::render('Books/Bestsellers', [
            'books' => $books,
        ]);
    }

    public function latest(Request $request): Response
    {
        $books = Book::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $books->getCollection()->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->price,
                'cover_image_url' => $book->cover_image_url,
                'category' => $book->category?->category_name,
                'format' => $book->format,
                'stock_quantity' => $book->stock_quantity,
                'description' => $book->description,
            ];
        });

        return Inertia::render('Books/Latest', [
            'books' => $books,
        ]);
    }

    public function byAuthor(Request $request, string $author): Response
    {
        $books = Book::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('author', 'LIKE', '%' . $author . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $books->getCollection()->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->price,
                'cover_image_url' => $book->cover_image_url,
                'category' => $book->category?->category_name,
                'format' => $book->format,
                'stock_quantity' => $book->stock_quantity,
                'description' => $book->description,
            ];
        });

        return Inertia::render('Books/ByAuthor', [
            'books' => $books,
            'author' => $author,
        ]);
    }
}