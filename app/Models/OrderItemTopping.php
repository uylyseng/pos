<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class OrderItemTopping extends Pivot
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_item_toppings';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_item_id',
        'product_topping_id',
        'price',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the order item that owns the topping.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product topping associated with this order item topping.
     */
    public function productTopping(): BelongsTo
    {
        return $this->belongsTo(ProductTopping::class);
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include toppings for a specific order item.
     */
    public function scopeForOrderItem(Builder $query, int $orderItemId): Builder
    {
        return $query->where('order_item_id', $orderItemId);
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Create a new model instance from raw attributes.
     * Required for using this model as a pivot in a belongsToMany relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  array  $attributes
     * @param  string  $table
     * @param  bool  $exists
     * @return static
     */
    public static function fromRawAttributes($parent, $attributes, $table = null, $exists = false)
    {
        $instance = new static;

        $instance->setRawAttributes($attributes, true);
        $instance->setTable($table ?: $instance->getTable());
        $instance->exists = $exists;
        $instance->setConnection($parent->getConnectionName());

        return $instance;
    }
}
