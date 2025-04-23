<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Size extends Model
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
     * Get the products associated with this size.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sizes')
            ->withPivot('price', 'is_active', 'created_by', 'updated_by')
            ->withTimestamps();
    }

    /**
     * Get the product sizes associated with this size.
     */
    public function productSizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }


    /**
     * Get the user who created the size.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the size.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active sizes.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search sizes by name in either language.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name_km', 'LIKE', "%{$term}%")
            ->orWhere('name_en', 'LIKE', "%{$term}%");
    }

    /**
     * Scope a query to order sizes by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter sizes by products they are associated with.
     */
    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->whereHas('productSizes', function (Builder $query) use ($productId) {
            $query->where('product_id', $productId);
        });
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
     * Get the count of products using this size.
     */
    public function getProductCountAttribute(): int
    {
        return $this->productSizes()->count();
    }

    /**
     * Get the count of active products using this size.
     */
    public function getActiveProductCountAttribute(): int
    {
        return $this->productSizes()->whereHas('product', function (Builder $query) {
            $query->where('is_active', true);
        })->count();
    }

    /**
     * Check if size is used by any products.
     */
    public function isUsed(): bool
    {
        return $this->productSizes()->exists();
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

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('size')
            ->setDescriptionForEvent(fn(string $eventName) => "Size {$this->name_km} has been {$eventName}")
            ->logOnly(['name_km', 'name_en', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
