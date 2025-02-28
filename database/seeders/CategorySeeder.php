<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin user ID for created_by field
        $adminId = User::where('email', 'lyseng@mail.com')
            ->orWhere('email', 'superadmin@example.com')
            ->value('id') ?? 1;

        // Define the categories
        $categories = [
            [
                'name_km' => 'កាហ្វេ',
                'name_en' => 'Coffee',
                'description' => 'កាហ្វេប្រភេទផ្សេងៗរួមមានអេស្ប្រេស្សូ កាពូជីណូ ឡាតេ អាមេរិកាណូ និងផ្សេងៗទៀត។',
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'name_km' => 'ភេសជ្ជៈ',
                'name_en' => 'Drinks',
                'description' => 'ភេសជ្ជៈផ្អែមឆ្ងាញ់ដូចជាអាល់ម៉ុន និងកាហ្វេ។',
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'name_km' => 'នំខេក',
                'name_en' => 'Cake',
                'description' => 'នំខេកផ្អែមឆ្ងាញ់និងនំផ្សេងៗរួមមាននំខេកសូកូឡា ឈីសខេក និងតាតផ្លែឈើ។',
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'name_km' => 'ភីហ្សា',
                'name_en' => 'Pizza',
                'description' => 'ភីហ្សាតាមបែបអ៊ីតាលីជាមួយគ្រឿងផ្សេងៗ មានទំហំខុសៗគ្នា។',
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'name_km' => 'មីហឹល',
                'name_en' => 'Noodle',
                'description' => 'មីហឹលស្រស់ស្អាតដែលមានគ្រឿងផ្សេងៗដូចជាបន្លែ និងសាច់ជ្រូក។',
                'is_active' => true,
                'created_by' => $adminId,
            ]
        ];

        // Loop through and create each category, avoiding duplicates
        foreach ($categories as $category) {
            Category::firstOrCreate(
                [
                    'name_km' => $category['name_km'],
                    'name_en' => $category['name_en']
                ],
                [
                    'description' => $category['description'],
                    'is_active' => $category['is_active'],
                    'created_by' => $category['created_by']
                ]
            );
        }

        $this->command->info('Added Coffee, Cake, and Pizza categories successfully');
    }
}
