<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductTopping extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'topping_id',
        'price',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the product topping.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the topping that owns the product topping.
     */
    public function topping(): BelongsTo
    {
        return $this->belongsTo(Topping::class);
    }

    /**
     * Get the user who created the product topping.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the product topping.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to filter by topping.
     */
    public function scopeForTopping(Builder $query, int $toppingId): Builder
    {
        return $query->where('topping_id', $toppingId);
    }

    /**
     * Scope a query to filter by active products.
     */
    public function scopeActiveProducts(Builder $query): Builder
    {
        return $query->whereHas('product', function (Builder $query) {
            $query->where('is_active', true);
        });
    }

    /**
     * Scope a query to filter by active toppings.
     */
    public function scopeActiveToppings(Builder $query): Builder
    {
        return $query->whereHas('topping', function (Builder $query) {
            $query->where('is_active', true);
        });
    }

    /**
     * Scope a query to get only fully active product toppings
     * (both product and topping are active).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by price.
     */
    public function scopeOrderByPrice(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('price', $direction);
    }

    /**
     * Scope a query to get recently added product toppings.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get the price difference from base product price.
     */
    public function getPriceDifferenceAttribute(): float
    {
        if (!$this->product) {
            return 0;
        }

        return $this->price - $this->product->base_price;
    }

    /**
     * Get the formatted price difference with sign.
     */
    public function getFormattedPriceDifferenceAttribute(): string
    {
        $difference = $this->priceDifference;

        if ($difference == 0) {
            return '$0.00';
        }

        $sign = $difference > 0 ? '+' : '';
        return $sign . '$' . number_format($difference, 2);
    }

    /**
     * Get the display name with topping and price.
     */
    public function getDisplayNameAttribute(): string
    {
        if (!$this->topping) {
            return $this->formattedPrice;
        }

        return $this->topping->displayName . ' - ' . $this->formattedPrice;
    }

    /**
     * Get whether this product topping is available
     * (both product and topping are active).
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->product &&
               $this->topping &&
               $this->product->is_active &&
               $this->topping->is_active;
    }

    /**
     * Get order items that use this product topping.
     */
    public function orderItems()
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_toppings')
            ->withPivot('price')
            ->withTimestamps();
    }

    /**
     * Get toppings for a specific product
     */
    public function getProductToppings($productId)
    {
        $productToppings = ProductTopping::where('product_id', $productId)
            ->join('toppings', 'product_toppings.topping_id', '=', 'toppings.id')
            ->select(
                'product_toppings.*',
                'toppings.name_km as topping_name_km',
                'toppings.name_en as topping_name_en'
            )
            ->get();

    return response()->json($productToppings);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Product Topping')
            ->setDescriptionForEvent(fn(string $eventName) => "Product Topping {$eventName}")
            ->logOnly(['product_id', 'topping_id', 'price', 'created_by', 'updated_by'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
