<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductSize extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'size_id',
        'multiplier',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'multiplier' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the product size.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the size that owns the product size.
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Get the user who created the product size.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the product size.
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
     * Scope a query to filter by size.
     */
    public function scopeForSize(Builder $query, int $sizeId): Builder
    {
        return $query->where('size_id', $sizeId);
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
     * Scope a query to filter by active sizes.
     */
    public function scopeActiveSizes(Builder $query): Builder
    {
        return $query->whereHas('size', function (Builder $query) {
            $query->where('is_active', true);
        });
    }

    /**
     * Scope a query to get only fully active product sizes
     * (both product and size are active).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by multiplier.
     */
    public function scopeOrderByMultiplier(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('multiplier', $direction);
    }

    /**
     * Get the calculated price based on product base price and size multiplier.
     */
    public function getCalculatedPriceAttribute(): float
    {
        if (!$this->product) {
            return 0;
        }

        return $this->product->base_price * $this->multiplier;
    }

    /**
     * Get the formatted calculated price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->calculated_price, 2);
    }

    /**
     * Get the price difference from base product price.
     */
    public function getPriceDifferenceAttribute(): float
    {
        if (!$this->product) {
            return 0;
        }

        return $this->calculated_price - $this->product->base_price;
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
     * Get the display name with size and price.
     */
    public function getDisplayNameAttribute(): string
    {
        if (!$this->size) {
            return $this->formattedPrice;
        }

        return $this->size->displayName . ' - ' . $this->formattedPrice;
    }

    /**
     * Get sizes for a specific product
     */
    public function getProductSizes($productId)
    {
        $productSizes = ProductSize::where('product_id', $productId)
            ->join('sizes', 'product_sizes.size_id', '=', 'sizes.id')
            ->select(
                'product_sizes.*',
                'sizes.name_km as size_name_km',
                'sizes.name_en as size_name_en'
            )
            ->get();

        return response()->json($productSizes);
    }

    /**
     * Get the size name in Khmer.
     */
    public function getSizeNameKmAttribute()
    {
        return $this->size->name_km ?? 'Size';
    }

    /**
     * Get the size name in English.
     */
    public function getSizeNameEnAttribute()
    {
        return $this->size->name_en ?? '';
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Product Size')
            ->setDescriptionForEvent(fn(string $eventName) => "Product Size has been {$eventName}")
            ->logOnly(['product_id', 'size_id', 'multiplier'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

}
