<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'is_default',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Currency $currency) {
            $currency->created_by = Auth::id();
            $currency->updated_by = Auth::id();
        });

        static::updating(function (Currency $currency) {
            $currency->updated_by = Auth::id();
        });

        // Automatically handle the is_default flag across currencies
        static::saved(function (Currency $currency) {
            if ($currency->is_default) {
                self::where('id', '!=', $currency->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the user who created the currency.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the currency.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include default currency.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Format an amount with the currency symbol.
     *
     * @param float|int $amount
     * @return string
     */
    public function format($amount): string
    {
        return $this->symbol . number_format($amount, 2);
    }

    /**
     * Get the default currency.
     *
     * @return self|null
     */
    public static function getDefault(): ?self
    {
        return self::default()->first();
    }

    /**
     * Check if this currency is the default.
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->is_default === true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('currency')
            ->setDescriptionForEvent(fn (string $eventName) => "Currency {$eventName}")
            ->logOnly(['name', 'code', 'symbol', 'is_default', 'is_active'])
            ->logOnlyDirty()
            ->setCreatedBy('created_by')
            ->setUpdatedBy('updated_by');
    }
}
