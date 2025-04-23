<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a default admin ID instead of looking up a user
        $adminId = 1; // Default admin ID

        // Get currency IDs - using the ones created by CurrencySeeder
        $usd = Currency::where('code', 'USD')->first();
        $khr = Currency::where('code', 'KHR')->first();

        if (!$usd || !$khr) {
            $this->command->error('Required currencies (USD/KHR) not found. Please run the CurrencySeeder first.');
            return;
        }

        // Define exchange rates with current date
        $today = Carbon::now();
        $oneMonthAgo = Carbon::now()->subMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        $fourMonthsAgo = Carbon::now()->subMonths(4);

        $exchangeRates = [
            // Current rates (using today's approximate rate)
            [
                'from_currency_id' => $usd->id,
                'to_currency_id' => $khr->id,
                'rate' => 4150.0000, // Updated to March 2025 approximate rate
                'start_date' => $today->copy()->subDays(7),
                'end_date' => null,
                'is_active' => true,
            ],
            [
                'from_currency_id' => $khr->id,
                'to_currency_id' => $usd->id,
                'rate' => 0.0002410, // 1/4150
                'start_date' => $today->copy()->subDays(7),
                'end_date' => null,
                'is_active' => true,
            ],

            // Previous rates (historical) - one month ago
            [
                'from_currency_id' => $usd->id,
                'to_currency_id' => $khr->id,
                'rate' => 4120.0000,
                'start_date' => $twoMonthsAgo,
                'end_date' => $today->copy()->subDays(8),
                'is_active' => true,
            ],
            [
                'from_currency_id' => $khr->id,
                'to_currency_id' => $usd->id,
                'rate' => 0.0002427, // 1/4120
                'start_date' => $twoMonthsAgo,
                'end_date' => $today->copy()->subDays(8),
                'is_active' => true,
            ],

            // Previous rates (historical) - three months ago
            [
                'from_currency_id' => $usd->id,
                'to_currency_id' => $khr->id,
                'rate' => 4080.0000,
                'start_date' => $fourMonthsAgo,
                'end_date' => $twoMonthsAgo->copy()->subDay(),
                'is_active' => true,
            ],
            [
                'from_currency_id' => $khr->id,
                'to_currency_id' => $usd->id,
                'rate' => 0.0002451, // 1/4080
                'start_date' => $fourMonthsAgo,
                'end_date' => $twoMonthsAgo->copy()->subDay(),
                'is_active' => true,
            ],
        ];

        // Create exchange rates
        $createdCount = 0;
        $updatedCount = 0;

        foreach ($exchangeRates as $rateData) {
            // Check if a similar rate exists based on currency pair and date range
            $existingRate = ExchangeRate::where('from_currency_id', $rateData['from_currency_id'])
                ->where('to_currency_id', $rateData['to_currency_id'])
                ->where('start_date', $rateData['start_date'])
                ->where(function($query) use ($rateData) {
                    if ($rateData['end_date']) {
                        $query->where('end_date', $rateData['end_date']);
                    } else {
                        $query->whereNull('end_date');
                    }
                })
                ->first();

            if (!$existingRate) {
                // Create new exchange rate
                ExchangeRate::create(array_merge($rateData, [
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]));
                $createdCount++;
            } else {
                // Update existing rate
                $existingRate->update([
                    'rate' => $rateData['rate'],
                    'is_active' => $rateData['is_active'],
                    'updated_by' => $adminId,
                ]);
                $updatedCount++;
            }
        }

        $this->command->info("Exchange rates seeded: {$createdCount} created, {$updatedCount} updated.");
    }
}
