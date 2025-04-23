<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'currency_id',
        'amount',
        'exchange_rate',
        'amount_in_default_currency',
        'status',
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
        'exchange_rate' => 'decimal:4',
        'amount_in_default_currency' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The payment status constants.
     *
     * @var array<string, string>
     */
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_FAILED = 'failed';

    /**
     * Get all available payment statuses.
     *
     * @return array<string, string>
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * Get the order associated with the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the payment method associated with the payment.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the currency associated with the payment.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the user who created the payment.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the payment.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include payments with a specific status.
     */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to include only completed payments.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to include only pending payments.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to include only failed payments.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope a query to filter payments by payment method.
     */
    public function scopeByPaymentMethod(Builder $query, int $paymentMethodId): Builder
    {
        return $query->where('payment_method_id', $paymentMethodId);
    }

    /**
     * Scope a query to filter payments by order.
     */
    public function scopeForOrder(Builder $query, int $orderId): Builder
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope a query to filter payments by date range.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(): self
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
        return $this;
    }

    /**
     * Mark payment as pending.
     */
    public function markAsPending(): self
    {
        $this->update(['status' => self::STATUS_PENDING]);
        return $this;
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(): self
    {
        $this->update(['status' => self::STATUS_FAILED]);
        return $this;
    }

    /**
     * Get the formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->currency) {
            return $this->currency->symbol . number_format($this->amount, 2);
        }

        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get the formatted default currency amount.
     */
    public function getFormattedDefaultAmountAttribute(): string
    {
        return '$' . number_format($this->amount_in_default_currency, 2);
    }

    /**
     * Get the status with appropriate class for styling.
     */
    public function getStatusBadgeAttribute(): array
    {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return [
                    'label' => 'Completed',
                    'class' => 'success',
                ];
            case self::STATUS_PENDING:
                return [
                    'label' => 'Pending',
                    'class' => 'warning',
                ];
            case self::STATUS_FAILED:
                return [
                    'label' => 'Failed',
                    'class' => 'danger',
                ];
            default:
                return [
                    'label' => ucfirst($this->status),
                    'class' => 'info',
                ];
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payment')
            ->setDescriptionForEvent(fn (string $eventName) => "Payment {$this->id} has been {$eventName}")
            ->logOnly(['order_id', 'payment_method_id', 'currency_id', 'amount', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
