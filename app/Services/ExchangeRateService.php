<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    /**
     * Default USD to KHR fallback rate
     */
    protected float $fallbackRate = 4100;

    /**
     * Get current USD to KHR exchange rate for a specific date
     *
     * @param Carbon|string|null $date Date to get rate for (defaults to now)
     * @return float Exchange rate value
     */
    public function getRate($date = null): float
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        // Try to get a valid exchange rate for the date
        $rate = ExchangeRate::where('from_currency_id', 1) // USD ID
            ->where('to_currency_id', 2)                   // KHR ID
            ->where('is_active', true)
            ->where(function($query) use ($date) {
                $query->where(function($q) use ($date) {
                    $q->where('start_date', '<=', $date)
                      ->where(function($sq) use ($date) {
                          $sq->where('end_date', '>=', $date)
                             ->orWhereNull('end_date');
                      });
                });
            })
            ->orderBy('start_date', 'desc')
            ->first();

        // Return rate or fallback
        return $rate ? (float)$rate->rate : $this->fallbackRate;
    }

    /**
     * Convert USD to KHR
     *
     * @param float $usdAmount Amount in USD
     * @param Carbon|string|null $date Date for exchange rate
     * @return float Amount in KHR
     */
    public function usdToKhr(float $usdAmount, $date = null): float
    {
        return $usdAmount * $this->getRate($date);
    }

    /**
     * Convert KHR to USD
     *
     * @param float $khrAmount Amount in KHR
     * @param Carbon|string|null $date Date for exchange rate
     * @return float Amount in USD
     */
    public function khrToUsd(float $khrAmount, $date = null): float
    {
        $rate = $this->getRate($date);
        return $rate > 0 ? $khrAmount / $rate : 0;
    }

    /**
     * Format a value as KHR currency
     *
     * @param float $amount Amount to format
     * @return string Formatted amount
     */
    public function formatKhr(float $amount): string
    {
        return '៛' . number_format(round($amount));
    }

    /**
     * Set fallback exchange rate
     *
     * @param float $rate New fallback rate
     * @return self
     */
    public function setFallbackRate(float $rate): self
    {
        $this->fallbackRate = $rate;
        return $this;
    }
}
