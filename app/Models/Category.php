<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
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
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the user who created the category.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the category.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search categories by name in either language.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name_km', 'LIKE', "%{$term}%")
            ->orWhere('name_en', 'LIKE', "%{$term}%");
    }

    /**
     * Scope a query to order categories by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter categories by a specific creator.
     */
    public function scopeCreatedByUser(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Get active products count for the category.
     */
    public function getActiveProductsCountAttribute(): int
    {
        return $this->products()->active()->count();
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
     * Check if category has any active products.
     */
    public function hasActiveProducts(): bool
    {
        return $this->products()->active()->exists();
    }

    /**
     * Get list of active products for this category.
     */
    public function activeProducts(): HasMany
    {
        return $this->products()->active();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('category')
            ->setDescriptionForEvent(fn(string $eventName) => "Category has been {$eventName}")
            ->logOnly(['name_km', 'name_en', 'description', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
