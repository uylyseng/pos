<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a default admin ID for created_by and updated_by
        $adminId = 1; // Default admin ID

        // Disable model events to prevent default overriding logic in model hooks
        // This allows us to explicitly control which currency is default
        Currency::withoutEvents(function () use ($adminId) {
            // Define the currencies
            $currencies = [
                [
                    'name' => 'US Dollar',
                    'code' => 'USD',
                    'symbol' => '$',
                    'is_default' => true,
                    'is_active' => true,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ],
                [
                    'name' => 'Cambodian Riel',
                    'code' => 'KHR',
                    'symbol' => '៛',
                    'is_default' => false,
                    'is_active' => true,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ],
            ];

            $this->command->info('Creating currencies...');

            foreach ($currencies as $currency) {
                Currency::firstOrCreate(
                    ['code' => $currency['code']],
                    $currency
                );
            }

            // Ensure only one default currency exists
            $defaultCurrencies = Currency::where('is_default', true)->count();
            if ($defaultCurrencies === 0) {
                // If no default currency exists, set USD as default
                $usd = Currency::where('code', 'USD')->first();
                if ($usd) {
                    $usd->update(['is_default' => true]);
                    $this->command->info('USD set as default currency');
                }
            } elseif ($defaultCurrencies > 1) {
                // If multiple default currencies exist, set only USD as default
                Currency::where('is_default', true)
                    ->where('code', '!=', 'USD')
                    ->update(['is_default' => false]);
                $this->command->info('Fixed multiple default currencies - USD is now the only default');
            }
        });

        $this->command->info('Currencies have been seeded successfully!');
    }
}
