<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a default admin ID instead of looking up a user
        $adminId = 1; // Default admin ID

        $toppings = [
            // Coffee toppings
            [
                'name_km' => 'ក្រេមខប់',
                'name_en' => 'Whipped Cream',
                'is_active' => true,
            ],
            [
                'name_km' => 'ស៊ីរ៉ូសូកូឡា',
                'name_en' => 'Chocolate Syrup',
                'is_active' => true,
            ],
            [
                'name_km' => 'ការ៉ាមែល',
                'name_en' => 'Caramel',
                'is_active' => true,
            ],
            [
                'name_km' => 'អំបិល',
                'name_en' => 'Sea Salt',
                'is_active' => true,
            ],
            [
                'name_km' => 'ទឹកឃ្មុំ',
                'name_en' => 'Honey',
                'is_active' => true,
            ],

            // Bubble Tea / Drink toppings
            [
                'name_km' => 'គ្រាប់គុជ',
                'name_en' => 'Boba Pearls',
                'is_active' => true,
            ],
            [
                'name_km' => 'វីឡី',
                'name_en' => 'Jelly',
                'is_active' => true,
            ],
            [
                'name_km' => 'គ្រាប់ប៊័រ',
                'name_en' => 'Popping Boba',
                'is_active' => true,
            ],
            [
                'name_km' => 'អាឡូ',
                'name_en' => 'Aloe Vera',
                'is_active' => true,
            ],
            [
                'name_km' => 'បន្ទាត់',
                'name_en' => 'Grass Jelly',
                'is_active' => true,
            ],
            [
                'name_km' => 'ប្រមា៉',
                'name_en' => 'Pudding',
                'is_active' => true,
            ],

            // Noodle toppings
            [
                'name_km' => 'ស៊ុត',
                'name_en' => 'Egg',
                'is_active' => true,
            ],
            [
                'name_km' => 'ស៊ុតជ្រូក',
                'name_en' => 'Chashu Pork',
                'is_active' => true,
            ],
            [
                'name_km' => 'គ្រឿងសមុទ្រ',
                'name_en' => 'Seafood',
                'is_active' => true,
            ],
            [
                'name_km' => 'បន្លែខៀវ',
                'name_en' => 'Green Onions',
                'is_active' => true,
            ],
            [
                'name_km' => 'ម្នោរី',
                'name_en' => 'Nori',
                'is_active' => true,
            ],
            [
                'name_km' => 'ម្ទេសសាច់',
                'name_en' => 'Fish Cake',
                'is_active' => true,
            ],
            [
                'name_km' => 'ដំឡូងជ្វា',
                'name_en' => 'Corn',
                'is_active' => true,
            ],
            [
                'name_km' => 'គ្រឿងខៀវ',
                'name_en' => 'Bean Sprouts',
                'is_active' => true,
            ],

            // Pizza toppings
            [
                'name_km' => 'ឈីសបន្ថែម',
                'name_en' => 'Extra Cheese',
                'is_active' => true,
            ],
            [
                'name_km' => 'បិបបេរូនី',
                'name_en' => 'Pepperoni',
                'is_active' => true,
            ],
            [
                'name_km' => 'ផ្សិត',
                'name_en' => 'Mushrooms',
                'is_active' => true,
            ],
            [
                'name_km' => 'ខ្ទឹមបារាំង',
                'name_en' => 'Onions',
                'is_active' => true,
            ],
            [
                'name_km' => 'ម្ទេសប៉េងប៉ោះ',
                'name_en' => 'Bell Peppers',
                'is_active' => true,
            ],
            [
                'name_km' => 'សាច់ក្រក',
                'name_en' => 'Sausage',
                'is_active' => true,
            ],
            [
                'name_km' => 'បិបបេរូនីបន្ថែម',
                'name_en' => 'Extra Pepperoni',
                'is_active' => true,
            ],
            [
                'name_km' => 'ហាឡាពេញ៉ូ',
                'name_en' => 'Jalapeños',
                'is_active' => true,
            ],
            [
                'name_km' => 'អូលីវខ្មៅ',
                'name_en' => 'Black Olives',
                'is_active' => true,
            ],
            [
                'name_km' => 'ស្ពីណាច',
                'name_en' => 'Spinach',
                'is_active' => true,
            ],
        ];

        $this->command->info('Creating toppings...');

        foreach ($toppings as $topping) {
            Topping::firstOrCreate(
                [
                    'name_en' => $topping['name_en'],
                ],
                [
                    'name_km' => $topping['name_km'],
                    'is_active' => $topping['is_active'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }

        $this->command->info('Toppings have been seeded successfully!');
    }
}
