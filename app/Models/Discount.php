<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Discount extends Model
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
        'description',
        'type',
        'amount',
        'min_purchase',
        'max_discount',
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
        'amount' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The possible discount types.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'percentage' => 'Percentage',
        'fixed' => 'Fixed Amount',
    ];

    /**
     * Get the user who created the discount.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the discount.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active discounts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by discount type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter discounts by minimum purchase amount.
     */
    public function scopeMinPurchase(Builder $query, float $amount): Builder
    {
        return $query->where('min_purchase', '<=', $amount);
    }

    /**
     * Scope a query to search discounts by name in either language.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name_km', 'LIKE', "%{$term}%")
            ->orWhere('name_en', 'LIKE', "%{$term}%");
    }

    /**
     * Scope a query to order discounts by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
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
     * Get a human-readable discount type.
     */
    public function getTypeDisplayAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Get the formatted amount with appropriate symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->type === 'percentage') {
            return number_format($this->amount, 0) . '%';
        }

        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get the formatted minimum purchase amount.
     */
    public function getFormattedMinPurchaseAttribute(): ?string
    {
        if ($this->min_purchase === null) {
            return null;
        }

        return '$' . number_format($this->min_purchase, 2);
    }

    /**
     * Get the formatted maximum discount amount.
     */
    public function getFormattedMaxDiscountAttribute(): ?string
    {
        if ($this->max_discount === null) {
            return null;
        }

        return '$' . number_format($this->max_discount, 2);
    }

    /**
     * Calculate the discount amount for a given purchase amount.
     */
    public function calculateDiscountAmount(float $purchaseAmount): float
    {
        // Check if minimum purchase requirement is met
        if ($this->min_purchase !== null && $purchaseAmount < $this->min_purchase) {
            return 0;
        }

        $discountAmount = 0;

        if ($this->type === 'percentage') {
            $discountAmount = ($purchaseAmount * $this->amount) / 100;

            // Apply maximum discount limit if specified
            if ($this->max_discount !== null && $discountAmount > $this->max_discount) {
                $discountAmount = $this->max_discount;
            }
        } else {
            // Fixed amount discount
            $discountAmount = $this->amount;
        }

        // Ensure discount doesn't exceed the purchase amount
        return min($discountAmount, $purchaseAmount);
    }

    /**
     * Check if discount is applicable to a given purchase amount.
     */
    public function isApplicableTo(float $purchaseAmount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->min_purchase !== null && $purchaseAmount < $this->min_purchase) {
            return false;
        }

        return true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('discount')
            ->setDescriptionForEvent(fn(string $eventName) => "Discount {$this->name_km} has been {$eventName}")
            ->logOnly(['name_km', 'name_en', 'description', 'type', 'amount', 'min_purchase', 'max_discount', 'is_active'])
            ->logOnlyDirty();
    }
}
