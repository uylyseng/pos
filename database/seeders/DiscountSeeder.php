<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a default admin ID instead of looking up a user
        $adminId = 1; // Default admin ID

        // Define discount data - just 4 modest discounts
        $discounts = [
            // Product-specific discounts
            [
                'name_km' => 'កាត់ពីតម្លៃកាហ្វេ ១០%',
                'name_en' => '10% Off Coffee Products',
                'description' => 'បញ្ចុះតម្លៃ ១០% លើផលិតផលកាហ្វេទាំងអស់',
                'type' => 'percentage',
                'amount' => 10,
                'min_purchase' => null,  // No minimum purchase required
                'max_discount' => 3,     // Max $3 discount to protect margins
                'is_active' => true,
            ],
            [
                'name_km' => 'បញ្ចុះតម្លៃលើភីហ្សា $២',
                'name_en' => '$2 Off Pizza',
                'description' => 'បញ្ចុះតម្លៃ $២ លើការបញ្ជាភីហ្សាណាមួយ',
                'type' => 'fixed',
                'amount' => 2,
                'min_purchase' => 10,    // Must purchase $10 or more
                'max_discount' => null,  // No max discount needed for small fixed amount
                'is_active' => true,
            ],

            // Order-type discounts
            [
                'name_km' => 'បញ្ចុះតម្លៃ​ការទិញច្រើន ៥%',
                'name_en' => '5% Bulk Purchase Discount',
                'description' => 'បញ្ចុះតម្លៃ ៥% សម្រាប់ការបញ្ជាលើស $៣០',
                'type' => 'percentage',
                'amount' => 5,
                'min_purchase' => 30,    // Reasonable minimum purchase
                'max_discount' => 8,     // Maximum $8 discount
                'is_active' => true,
            ],
            [
                'name_km' => 'ប្រមូសិនយកទៅ $១',
                'name_en' => '$1 Takeaway Promotion',
                'description' => 'បញ្ចុះតម្លៃ $១ សម្រាប់ការបញ្ជាទាំងអស់ដែលយកទៅខាងក្រៅ',
                'type' => 'fixed',
                'amount' => 1,
                'min_purchase' => 10,    // Must spend $10 or more
                'max_discount' => null,  // No max discount needed for small fixed amount
                'is_active' => true,
            ]
        ];

        $this->command->info('Creating discounts...');

        // Create discounts if they don't exist, otherwise update them
        foreach ($discounts as $discountData) {
            Discount::firstOrCreate(
                [
                    'name_en' => $discountData['name_en'],
                ],
                array_merge($discountData, [
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ])
            );
        }

        $this->command->info('Created ' . count($discounts) . ' discount records');
    }
}
