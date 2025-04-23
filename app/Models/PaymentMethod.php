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

class PaymentMethod extends Model
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
     * Get the payments associated with the payment method.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user who created the payment method.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the payment method.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active payment methods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive payment methods.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to search payment methods by name in either language.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name_km', 'LIKE', "%{$term}%")
            ->orWhere('name_en', 'LIKE', "%{$term}%");
    }

    /**
     * Scope a query to order payment methods by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get payment methods ordered by name in the current locale.
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
     * Get name attribute for backward compatibility.
     * This helps with queries that might be looking for 'name'
     */
    public function getNameAttribute(): string
    {
        return $this->getDisplayNameAttribute();
    }

    /**
     * Get the total payment amount for this payment method.
     */
    public function getTotalPaymentsAmountAttribute(): float
    {
        return $this->payments()
            ->completed()
            ->sum('amount_in_default_currency');
    }

    /**
     * Get the count of completed payments for this payment method.
     */
    public function getCompletedPaymentsCountAttribute(): int
    {
        return $this->payments()->completed()->count();
    }

    /**
     * Get the status label with appropriate class for styling.
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
            ->useLogName('payment_method')
            ->setDescriptionForEvent(fn (string $eventName) => "Payment method has been {$eventName}")
            ->logOnly(['name_km', 'name_en', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
