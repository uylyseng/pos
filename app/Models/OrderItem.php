<?php

namespace App\Models;

use FontLib\Table\Type\loca;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_size_id',
        'product_topping_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product size for this order item.
     */
    public function productSize(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }

    /**
     * Get the size for this order item.
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'product_size_id', 'id')
            ->withDefault();
    }

    /**
     * Get the product topping for this order item.
     */
    public function topping(): BelongsTo
    {
        return $this->belongsTo(ProductTopping::class, 'product_topping_id');
    }

    /**
     * Get all toppings for this order item.
     */
    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(ProductTopping::class, 'order_item_toppings')
            ->withPivot('price')
            ->withTimestamps()
            ->using(OrderItemTopping::class);
    }

    /**
     * Get the user who created the order item.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the order item.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by order.
     */
    public function scopeForOrder(Builder $query, int $orderId): Builder
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope a query to filter by product.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter items with specific toppings.
     */
    public function scopeWithTopping(Builder $query, int $toppingId): Builder
    {
        return $query->whereHas('toppings', function (Builder $query) use ($toppingId) {
            $query->where('product_topping_id', $toppingId);
        });
    }

    /**
     * Scope a query to sort by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to sort by quantity.
     */
    public function scopeOrderByQuantity(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('quantity', $direction);
    }

    /**
     * Scope a query to get items with a minimum quantity.
     */
    public function scopeMinQuantity(Builder $query, int $quantity): Builder
    {
        return $query->where('quantity', '>=', $quantity);
    }

    /**
     * Get the formatted unit price with currency symbol.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        $currency = $this->order->currency ?? null;
        $symbol = $currency ? $currency->symbol : '$';

        return $symbol . number_format($this->unit_price, 2);
    }

    /**
     * Get the formatted subtotal with currency symbol.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        $currency = $this->order->currency ?? null;
        $symbol = $currency ? $currency->symbol : '$';

        return $symbol . number_format($this->subtotal, 2);
    }

    /**
     * Get the product name for display.
     */
    public function getProductNameAttribute(): ?string
    {
        return $this->product ? $this->product->displayName : null;
    }

    /**
     * Get the size name for display.
     */
    public function getSizeNameAttribute(): ?string
    {
        return $this->size && $this->size->size ? $this->size->size->displayName : null;
    }

    /**
     * Get the topping name for display.
     */
    public function getToppingNameAttribute(): ?string
    {
        return $this->topping && $this->topping->topping ? $this->topping->topping->displayName : null;
    }

    /**
     * Get a list of all topping names for this item.
     */
    public function getToppingNamesAttribute(): array
    {
        return $this->toppings->map(function ($topping) {
            return $topping->topping ? $topping->topping->displayName : null;
        })->filter()->toArray();
    }

    /**
     * Get a formatted display name for the order item.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->productName ?? 'Unknown Product';

        if ($this->sizeName) {
            $name .= ' - ' . $this->sizeName;
        }

        if (!empty($this->toppingNames)) {
            $name .= ' with ' . implode(', ', $this->toppingNames);
        }

        return $name;
    }

    /**
     * Calculate and set the subtotal based on quantity and unit price.
     */
    public function calculateSubtotal(): self
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        return $this;
    }

    /**
     * Update the quantity and recalculate subtotal.
     */
    public function updateQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        $this->calculateSubtotal();
        $this->save();

        // Update order totals
        if ($this->order) {
            $this->order->recalculateTotals();
        }

        return $this;
    }

    /**
     * Add a topping to this order item.
     */
    public function addTopping(ProductTopping $productTopping): self
    {
        $this->toppings()->attach($productTopping->id, [
            'price' => $productTopping->price
        ]);

        // Update order totals
        if ($this->order) {
            $this->order->recalculateTotals();
        }

        return $this;
    }

    /**
     * Remove a topping from this order item.
     */
    public function removeTopping(int $productToppingId): self
    {
        $this->toppings()->detach($productToppingId);

        // Update order totals
        if ($this->order) {
            $this->order->recalculateTotals();
        }

        return $this;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate subtotal when creating or updating
        static::saving(function ($orderItem) {
            $orderItem->calculateSubtotal();
        });
    }

    public function getActivitylogOptions(): logOptions
    {
        return LogOptions::defaults()
            ->useLogName('Order Item')
            ->setDescriptionForEvent(fn(string $eventName) => "Order item has been {$eventName}")
            ->logOnly(['order_id', 'product_id', 'product_size_id', 'product_topping_id', 'quantity', 'unit_price', 'subtotal', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
