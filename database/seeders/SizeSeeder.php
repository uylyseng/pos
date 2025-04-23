<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a default admin ID instead of looking up a user
        $adminId = 1; // Default admin ID

        $sizes = [
            [
                'name_km' => 'តូច',
                'name_en' => 'Small',
                'is_active' => true,
            ],
            [
                'name_km' => 'មធ្យម',
                'name_en' => 'Medium',
                'is_active' => true,
            ],
            [
                'name_km' => 'ធំ',
                'name_en' => 'Large',
                'is_active' => true,
            ],
            [
                'name_km' => 'ធំបំផុត',
                'name_en' => 'Extra Large',
                'is_active' => true,
            ],
            [
                'name_km' => 'មួយចំណិត',
                'name_en' => 'Slice',
                'is_active' => true,
            ],
            [
                'name_km' => 'ទាំងមូល',
                'name_en' => 'Whole',
                'is_active' => true,
            ],
        ];

        $this->command->info('Creating sizes...');

        foreach ($sizes as $size) {
            Size::firstOrCreate(
                [
                    'name_en' => $size['name_en'],
                ],
                [
                    'name_km' => $size['name_km'],
                    'is_active' => $size['is_active'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }

        $this->command->info('Sizes have been seeded successfully!');
    }
}
