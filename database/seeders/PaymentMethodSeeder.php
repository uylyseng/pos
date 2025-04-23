<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a default admin ID instead of looking up a user
        $adminId = 1; // Default admin ID

        $paymentMethods = [
            [
                'name_km' => 'សាច់ប្រាក់',
                'name_en' => 'Cash',
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'name_km' => 'កាតឥណទាន',
                'name_en' => 'Credit',
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'name_km' => 'ទូរស័ព្ទចល័ត',
                'name_en' => 'Mobile',
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        ];

        $this->command->info('Creating payment methods...');

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(
                ['name_en' => $method['name_en']],
                $method
            );
        }

        $this->command->info('Payment methods have been seeded successfully!');
    }
}
