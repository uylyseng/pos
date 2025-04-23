<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Trait\LogsActivity;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'table_number',
        'currency_id',
        'subtotal',
        'total',
        'discount_id',
        'discount_amount',
        'exchange_rate',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the currency used for the order.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the discount applied to the order.
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the user who created the order.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the order.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to filter orders by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter orders by table number.
     */
    public function scopeByTable(Builder $query, string $tableNumber): Builder
    {
        return $query->where('table_number', $tableNumber);
    }

    /**
     * Scope a query to filter orders by date range.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter orders created today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope a query to filter orders by minimum total amount.
     */
    public function scopeMinimumAmount(Builder $query, float $amount): Builder
    {
        return $query->where('total', '>=', $amount);
    }

    /**
     * Scope a query to filter orders with a discount applied.
     */
    public function scopeWithDiscount(Builder $query): Builder
    {
        return $query->whereNotNull('discount_id')->where('discount_amount', '>', 0);
    }

    /**
     * Scope a query to sort orders by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the total number of items in the order.
     */
    public function getTotalItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get the formatted subtotal with currency symbol.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        if ($this->currency) {
            return $this->currency->symbol . number_format($this->subtotal, 2);
        }

        return '$' . number_format($this->subtotal, 2);
    }

    /**
     * Get the total formatted with currency symbol.
     */
    public function getFormattedTotalAttribute(): string
    {
        if ($this->currency) {
            return $this->currency->symbol . number_format($this->total, 2);
        }

        return '$' . number_format($this->total, 2);
    }

    /**
     * Get the discount amount formatted with currency symbol.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        if ($this->currency) {
            return $this->currency->symbol . number_format($this->discount_amount, 2);
        }

        return '$' . number_format($this->discount_amount, 2);
    }

    /**
     * Check if the order has a discount applied.
     */
    public function hasDiscount(): bool
    {
        return $this->discount_id !== null && $this->discount_amount > 0;
    }

    /**
     * Get the total amount of payments made for this order.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->completed()->sum('amount_in_default_currency');
    }

    /**
     * Get the remaining balance to be paid on the order.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total - $this->totalPaid);
    }

    /**
     * Check if the order is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->remainingBalance <= 0;
    }

    /**
     * Add an item to the order.
     */
    public function addItem(array $itemData): OrderItem
    {
        return $this->items()->create($itemData);
    }

    /**
     * Apply a discount to the order.
     */
    public function applyDiscount(Discount $discount): self
    {
        // Calculate the discount amount based on the current subtotal
        $discountAmount = $discount->calculateDiscountAmount($this->subtotal);

        $this->update([
            'discount_id' => $discount->id,
            'discount_amount' => $discountAmount,
            'total' => $this->subtotal - $discountAmount
        ]);

        return $this;
    }

    /**
     * Recalculate the order totals.
     */
    public function recalculateTotals(): self
    {
        $subtotal = $this->items()->sum(\DB::raw('price * quantity'));

        // Apply existing discount if there is one
        $discountAmount = 0;
        if ($this->discount_id && $this->discount) {
            $discountAmount = $this->discount->calculateDiscountAmount($subtotal);
        }

        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total' => $subtotal - $discountAmount
        ]);

        return $this;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('order')
            ->setDescriptionForEvent(fn (string $eventName) => "Order {$this->id} has been {$eventName}")
            ->logOnly(['user_id', 'table_number', 'currency_id', 'subtotal', 'total', 'discount_id', 'discount_amount'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
