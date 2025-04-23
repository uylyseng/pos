<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use function PHPUnit\Framework\logicalNot;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name_km',
        'name_en',
        'description',
        'base_price',
        'image',
        'category_id',
        'has_sizes',
        'has_toppings',
        'is_stock',
        'quantity',
        'low_stock_threshold',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_price' => 'decimal:2',
        'has_sizes' => 'boolean',
        'has_toppings' => 'boolean',
        'is_stock' => 'boolean',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all sizes associated with the product through ProductSize model.
     */
    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_sizes')
                    ->withPivot('price', 'is_active', 'created_by', 'updated_by')
                    ->withTimestamps();
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who created the product.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the product.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all sizes for the product.
     */
    public function productSizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    /**
     * Get all toppings for the product.
     */
    public function productToppings(): HasMany
    {
        return $this->hasMany(ProductTopping::class);
    }


    /**
     * Get all toppings associated with the product through ProductTopping model.
     */
    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'product_toppings')
                    ->withPivot('price', 'is_active', 'created_by', 'updated_by')
                    ->withTimestamps();
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include products with low stock.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('is_stock', true)
            ->whereColumn('quantity', '<', 'low_stock_threshold');
    }

    /**
     * Scope a query to only include products in a specific category.
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter products by feature (sizes/toppings).
     */
    public function scopeWithFeature(Builder $query, string $feature, bool $value = true): Builder
    {
        if (!in_array($feature, ['has_sizes', 'has_toppings'])) {
            return $query;
        }

        return $query->where($feature, $value);
    }

    /**
     * Scope a query to search by name in either language.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name_km', 'LIKE', "%{$term}%")
            ->orWhere('name_en', 'LIKE', "%{$term}%");
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->is_stock && $this->quantity <= 0;
    }

    /**
     * Check if product has low stock.
     */
    public function hasLowStock(): bool
    {
        return $this->is_stock && $this->quantity < $this->low_stock_threshold;
    }

    /**
     * Get the formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->base_price, 2);
    }

    /**
     * Get the display name based on locale.
     */
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();

        if ($locale === 'km' || empty($this->name_en)) {
            return $this->name_km;
        }

        return $this->name_en;
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$this->name_km} has been {$eventName}")
            ->useLogName('product')
            ->logOnly(['name_km', 'name_en', 'base_price', 'image', 'category_id', 'has_sizes', 'has_toppings', 'is_stock', 'quantity', 'low_stock_threshold', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
