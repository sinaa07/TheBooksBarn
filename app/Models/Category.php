<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_name',
        'description',
        'slug',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate slug from category name if not provided
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->category_name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('category_name') && empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->category_name);
            }
        });
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function activeBooks()
    {
        return $this->hasMany(Book::class)->where('is_active', true);
    }

    public function featuredBooks()
    {
        return $this->hasMany(Book::class)->where('featured', true)->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive categories.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }


    public function scopeWithActiveBooks($query)
    {
        return $query->whereHas('books', function ($query) {
            $query->where('is_active', true)->where('stock_quantity', '>', 0);
        });
    }

    public function scopeWithFeaturedBooks($query)
    {
        return $query->whereHas('books', function ($query) {
            $query->where('featured', true)->where('is_active', true);
        });
    }

    public function scopeOrderByBookCount($query, $direction = 'desc')
    {
        return $query->withCount(['activeBooks'])
            ->orderBy('active_books_count', $direction);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('category_name', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }


    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }


    public function getActiveBooksCount(): int
    {
        return $this->activeBooks()->count();
    }

    public function getTotalStock(): int
    {
        return $this->activeBooks()->sum('stock_quantity');
    }

    public function getAveragePrice(): float
    {
        return $this->activeBooks()->avg('price') ?? 0.0;
    }

    public function getPriceRange(): array
    {
        $books = $this->activeBooks();
        
        return [
            'min' => $books->min('price') ?? 0,
            'max' => $books->max('price') ?? 0,
        ];
    }


    public function canBeDeleted(): bool
    {
        return $this->books()->count() === 0;
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }


    public static function getPopular(int $limit = 10)
    {
        return static::active()
            ->withCount(['activeBooks'])
            ->orderBy('active_books_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getValidationRules(int $categoryId = null): array
    {
        $slugRule = 'required|string|max:100|unique:categories,slug';
        if ($categoryId) {
            $slugRule .= ',' . $categoryId;
        }

        return [
            'category_name' => 'required|string|max:100|unique:categories,category_name' . ($categoryId ? ',' . $categoryId : ''),
            'description' => 'nullable|string',
            'slug' => $slugRule,
            'is_active' => 'boolean',
        ];
    }
}