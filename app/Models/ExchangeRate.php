<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Carbon\Carbon;

class ExchangeRate extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rate',
        'start_date',
        'end_date',
        'is_active',
        'from_currency_id',
        'to_currency_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rate' => 'decimal:4',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the source currency for this exchange rate.
     */
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    /**
     * Get the target currency for this exchange rate.
     */
    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    /**
     * Get the user who created the exchange rate.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the exchange rate.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active exchange rates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter exchange rates by specific currency pair.
     */
    public function scopeForCurrencyPair(Builder $query, int $fromCurrencyId, int $toCurrencyId): Builder
    {
        return $query->where('from_currency_id', $fromCurrencyId)
            ->where('to_currency_id', $toCurrencyId);
    }

    /**
     * Scope a query to filter exchange rates valid at a specific date.
     */
    public function scopeValidAt(Builder $query, null|string|\Carbon\Carbon $date = null): Builder
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        return $query->where('start_date', '<=', $date)
            ->where(function (Builder $query) use ($date) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $date);
            });
    }

    /**
     * Scope a query to get current active rates.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->active()->validAt();
    }

    /**
     * Scope a query to get rates that have expired.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('end_date', '<', Carbon::now());
    }

    /**
     * Scope a query to get rates for a specific source currency.
     */
    public function scopeFromCurrency(Builder $query, int $currencyId): Builder
    {
        return $query->where('from_currency_id', $currencyId);
    }

    /**
     * Scope a query to get rates for a specific target currency.
     */
    public function scopeToCurrency(Builder $query, int $currencyId): Builder
    {
        return $query->where('to_currency_id', $currencyId);
    }

    /**
     * Scope a query to sort exchange rates by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the display name for this exchange rate.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->fromCurrency && $this->toCurrency) {
            return "{$this->fromCurrency->code}/{$this->toCurrency->code}";
        }

        return 'Unknown Currency Pair';
    }

    /**
     * Get the formatted rate attribute.
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 4);
    }

    /**
     * Get the inverse rate value.
     */
    public function getInverseRateAttribute(): float
    {
        return $this->rate > 0 ? 1 / $this->rate : 0;
    }

    /**
     * Get the formatted inverse rate.
     */
    public function getFormattedInverseRateAttribute(): string
    {
        return number_format($this->inverseRate, 4);
    }

    /**
     * Check if the exchange rate is currently valid.
     */
    public function isValid(): bool
    {
        $now = Carbon::now();
        return $this->is_active &&
               $this->start_date <= $now &&
               ($this->end_date === null || $this->end_date >= $now);
    }

    /**
     * Check if the exchange rate is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date !== null && $this->end_date < Carbon::now();
    }

    /**
     * Convert an amount from source currency to target currency.
     */
    public function convert(float $amount): float
    {
        return $amount * $this->rate;
    }

    /**
     * Get the status badge for UI display.
     */
    public function getStatusBadgeAttribute(): array
    {
        if (!$this->is_active) {
            return [
                'label' => 'Inactive',
                'class' => 'danger',
            ];
        }

        if ($this->isExpired()) {
            return [
                'label' => 'Expired',
                'class' => 'warning',
            ];
        }

        return [
            'label' => 'Active',
            'class' => 'success',
        ];
    }

    /**
     * Get the current valid exchange rate for a currency pair.
     */
    public static function getCurrentRate(int $fromCurrencyId, int $toCurrencyId): ?self
    {
        return static::forCurrencyPair($fromCurrencyId, $toCurrencyId)
            ->current()
            ->orderBy('start_date', 'desc')
            ->first();
    }

    /**
     * Set the exchange rate as inactive.
     */
    public function deactivate(): self
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    /**
     * Set an end date for the exchange rate.
     */
    public function setEndDate(null|string|\Carbon\Carbon $date = null): self
    {
        $endDate = $date ? Carbon::parse($date) : Carbon::now();

        $this->update(['end_date' => $endDate]);
        return $this;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('exchange_rate')
            ->setDescriptionForEvent(fn (string $eventName) => "Exchange rate {$this->fromCurrency->code}/{$this->toCurrency->code} has been {$eventName}")
            ->logOnly(['rate', 'start_date', 'end_date', 'is_active', 'from_currency_id', 'to_currency_id']);
    }
}
