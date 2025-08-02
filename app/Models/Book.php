<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'isbn',
        'title',
        'author',
        'category_id',
        'description',
        'price',
        'stock_quantity',
        'format',
        'cover_image_url',
        'is_active',
        'featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'featured' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure featured books are active
        static::saving(function ($book) {
            if ($book->featured && !$book->is_active) {
                $book->featured = false;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price, 2);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= 5) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getAvailabilityAttribute(): string
    {
        if (!$this->is_active) {
            return 'unavailable';
        }
        
        return $this->stock_status;
    }

    public function getCoverImageAttribute(): string
    {
        return $this->cover_image_url ?: asset('images/default-book-cover.jpg');
    }

    public function getShortDescriptionAttribute(): string
    {
        if (!$this->description) {
            return '';
        }
        
        return strlen($this->description) > 150 
            ? substr($this->description, 0, 150) . '...' 
            : $this->description;
    }

    public function getSlugAttribute(): string
    {
        return str_replace(' ', '-', strtolower($this->title)) . '-' . $this->id;
    }


    public function getUrlAttribute(): string
    {
        return route('books.show', $this->slug);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true)->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('stock_quantity', '<=', $threshold)->where('stock_quantity', '>', 0);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeOfFormat($query, string $format)
    {
        return $query->where('format', $format);
    }

    public function scopePriceBetween($query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->whereFullText(['title', 'author', 'description'], $term)
            ->orWhere(function ($query) use ($term) {
                $query->where('title', 'LIKE', "%{$term}%")
                      ->orWhere('author', 'LIKE', "%{$term}%")
                      ->orWhere('isbn', 'LIKE', "%{$term}%");
            });
    }

    public function scopePopular($query)
    {
        return $query->withCount('orderItems')
            ->orderBy('order_items_count', 'desc');
    }

    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isFeatured(): bool
    {
        return $this->featured && $this->is_active;
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->isInStock();
    }

    public function hasLowStock(int $threshold = 5): bool
    {
        return $this->stock_quantity <= $threshold && $this->stock_quantity > 0;
    }

    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false, 'featured' => false]);
    }

    public function makeFeatured(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        return $this->update(['featured' => true]);
    }

    public function removeFeatured(): bool
    {
        return $this->update(['featured' => false]);
    }

    public function increaseStock(int $quantity): bool
    {
        return $this->increment('stock_quantity', $quantity);
    }

    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }
        
        return $this->decrement('stock_quantity', $quantity);
    }

    public function updateStock(int $quantity): bool
    {
        return $this->update(['stock_quantity' => max(0, $quantity)]);
    }

    public function getTotalSold(): int
    {
        return $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->whereIn('order_status', ['delivered', 'shipped']);
            })
            ->sum('quantity');
    }

    public function getTotalRevenue(): float
    {
        return $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->whereIn('order_status', ['delivered', 'shipped']);
            })
            ->sum('total_price');
    }

    public function getRelatedBooks(int $limit = 8)
    {
        return static::active()
            ->inStock()
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->limit($limit)
            ->get();
    }


    public function canAddToCart(int $quantity = 1): bool
    {
        return $this->isAvailable() && $this->stock_quantity >= $quantity;
    }

    public static function getValidationRules(int $bookId = null): array
    {
        $isbnRule = 'nullable|string|max:17|unique:books,isbn';
        if ($bookId) {
            $isbnRule .= ',' . $bookId;
        }

        return [
            'isbn' => $isbnRule,
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'stock_quantity' => 'required|integer|min:0',
            'format' => 'required|in:hardcover,paperback,ebook',
            'cover_image_url' => 'nullable|string|max:500|url',
            'is_active' => 'boolean',
            'featured' => 'boolean',
        ];
    }
}