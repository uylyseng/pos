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

class Topping extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_km',
        'name_en',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the product toppings associated with this topping.
     */
    public function productToppings(): HasMany
    {
        return $this->hasMany(ProductTopping::class);
    }

    /**
     * Get the products associated with this topping.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_toppings')
            ->withPivot('price', 'is_active', 'created_by', 'updated_by')
            ->withTimestamps();
    }

    /**
     * Get the order items associated with this topping.
     */
    public function orderItems(): BelongsToMany
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_toppings', 'product_topping_id', 'order_item_id')
            ->using(OrderItemTopping::class)
            ->withPivot('price')
            ->withTimestamps();
    }

    /**
     * Get the user who created the topping.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the topping.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active toppings.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search toppings by name in either language.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name_km', 'LIKE', "%{$term}%")
            ->orWhere('name_en', 'LIKE', "%{$term}%");
    }

    /**
     * Scope a query to order toppings by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter toppings by products they are associated with.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->whereHas('productToppings', function (Builder $query) use ($productId) {
            $query->where('product_id', $productId);
        });
    }

    /**
     * Scope a query to get toppings ordered by name in the current locale.
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): Builder
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'km' ? 'name_km' : 'name_en';

        return $query->orderBy($nameColumn, $direction);
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
     * Get the count of products using this topping.
     */
    public function getProductCountAttribute(): int
    {
        return $this->productToppings()->count();
    }

    /**
     * Get the count of active products using this topping.
     */
    public function getActiveProductCountAttribute(): int
    {
        return $this->productToppings()->whereHas('product', function (Builder $query) {
            $query->where('is_active', true);
        })->count();
    }

    /**
     * Check if topping is used by any products.
     */
    public function isUsed(): bool
    {
        return $this->productToppings()->exists();
    }

    /**
     * Get the price for a specific product.
     */
    public function getPriceForProduct(int $productId): ?float
    {
        $productTopping = $this->productToppings()
            ->where('product_id', $productId)
            ->first();

        return $productTopping ? $productTopping->price : null;
    }

    /**
     * Get the status badge for UI display.
     */
    public function getStatusBadgeAttribute(): array
    {
        return [
            'label' => $this->is_active ? 'Active' : 'Inactive',
            'class' => $this->is_active ? 'success' : 'danger',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('topping')
            ->setDescriptionForEvent(fn (string $eventName) => "Topping {$this->name_km} has been {$eventName}")
            ->logOnly(['name_km', 'name_en', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
