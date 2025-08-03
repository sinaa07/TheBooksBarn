<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseBookController extends Controller
{
    /**
     * Apply filtering for books based on request parameters.
     */
    protected function applyBookFilters(Builder $query, Request $request): Builder
    {
        $format   = $request->get('format');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        // Always exclude out-of-stock books
        $query->where('stock_quantity', '>', 0);

        if (!empty($format)) {
            $query->where('format', $format);
        }

        // Allow "0" as a valid price filter
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', (float) $minPrice);
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', (float) $maxPrice);
        }

        return $query;
    }

    /**
     * Apply sorting for books based on sort_by parameter.
     */
    protected function applyBookSorting(Builder $query, string $sortBy = 'newest'): Builder
    {
        return match ($sortBy) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'title'      => $query->orderBy('title', 'asc'),
            'oldest'     => $query->orderBy('created_at', 'asc'),
            default      => $query->orderBy('created_at', 'desc'), // newest
        };
    }

    /**
     * Get available book formats (filtered by category if provided).
     */
    protected function getBookFormats(?int $categoryId = null): \Illuminate\Support\Collection
    {
        $query = Book::select('format')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->distinct()
            ->orderBy('format');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->pluck('format');
    }

    /**
     * Get the min/max price range for available books.
     */
    protected function getBookPriceRange(?int $categoryId = null): object
    {
        $query = Book::where('is_active', true)
            ->where('stock_quantity', '>', 0);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $priceRange = $query
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        if (!$priceRange || $priceRange->min_price === null) {
            return (object) ['min_price' => 0, 'max_price' => 100];
        }

        return (object) [
            'min_price' => (float) $priceRange->min_price,
            'max_price' => (float) $priceRange->max_price,
        ];
    }

    /**
     * Transform paginated books into a consistent array structure.
     */
    protected function transformBookCollection(LengthAwarePaginator $books): void
    {
        $books->getCollection()->transform(function ($book) {
            return [
                'id'             => $book->id,
                'title'          => $book->title,
                'author'         => $book->author,
                'price'          => $book->price,
                'cover_image_url'=> $book->cover_image_url,
                'category'       => $book->category?->category_name ?? null,
                'category_id'    => $book->category_id,
                'format'         => $book->format,
                'stock_quantity' => $book->stock_quantity,
                'description'    => $book->description,
            ];
        });
    }
}
