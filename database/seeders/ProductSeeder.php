<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductSize;
use App\Models\Topping;
use App\Models\ProductTopping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use a default admin ID instead of looking up a user
        $adminId = 1; // Default admin ID

        // Get category IDs
        $coffeeCategory = Category::where('name_en', 'Coffee')->first();
        $drinksCategory = Category::where('name_en', 'Drinks')->first();
        $cakeCategory = Category::where('name_en', 'Cake')->first();
        $pizzaCategory = Category::where('name_en', 'Pizza')->first();
        $noodleCategory = Category::where('name_en', 'Noodle')->first();

        if (!$coffeeCategory || !$drinksCategory || !$cakeCategory || !$pizzaCategory || !$noodleCategory) {
            $this->command->error('Categories not found. Please run the CategorySeeder first.');
            return;
        }

        // Define products
        $products = [
            // Coffee products
            [
                'name_km' => 'អាមេរិកាណូ',
                'name_en' => 'Americano',
                'description' => 'ភេសជ្ជៈកាហ្វេដែលធ្វើឡើងដោយការបន្ថែមទឹកក្ដៅទៅក្នុងអេស្ប្រេស្សូ។',
                'base_price' => 2.50,
                'category_id' => $coffeeCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Small', 'multiplier' => 1.0],
                    ['name' => 'Medium', 'multiplier' => 1.4],
                    ['name' => 'Large', 'multiplier' => 1.8],
                ],
                'toppings' => [
                    ['name' => 'Whipped Cream', 'price' => 0.50],
                    ['name' => 'Chocolate Syrup', 'price' => 0.75],
                    ['name' => 'Caramel', 'price' => 0.75],
                ]
            ],
            [
                'name_km' => 'កាពុជីណូ',
                'name_en' => 'Cappuccino',
                'description' => 'ភេសជ្ជៈកាហ្វេដែលមានអេស្ប្រេស្សូ ទឹកដោះគោចំហុយ និងទឹកដោះគោ។',
                'base_price' => 3.00,
                'category_id' => $coffeeCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Small', 'multiplier' => 1.0],
                    ['name' => 'Medium', 'multiplier' => 1.30],
                    ['name' => 'Large', 'multiplier' => 1.60],
                ],
                'toppings' => [
                    ['name' => 'Whipped Cream', 'price' => 0.50],
                    ['name' => 'Chocolate Syrup', 'price' => 0.75],
                    ['name' => 'Caramel', 'price' => 0.75],
                ]
            ],
            [
                'name_km' => 'ឡាតេ',
                'name_en' => 'Latte',
                'description' => 'ភេសជ្ជៈកាហ្វេដែលមានទឹកដោះគោច្រើន និងអេស្ប្រេស្សូ។',
                'base_price' => 3.25,
                'category_id' => $coffeeCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Small', 'multiplier' => 1.0],
                    ['name' => 'Medium', 'multiplier' => 1.30],
                    ['name' => 'Large', 'multiplier' => 1.60],
                ],
                'toppings' => [
                    ['name' => 'Whipped Cream', 'price' => 0.50],
                    ['name' => 'Chocolate Syrup', 'price' => 0.75],
                    ['name' => 'Caramel', 'price' => 0.75],
                    ['name' => 'Sea Salt', 'price' => 0.50],
                    ['name' => 'Honey', 'price' => 0.75],
                ]
            ],

            // Drinks products
            [
                'name_km' => 'តែបុប្ផាឈូក',
                'name_en' => 'Bubble Milk Tea',
                'description' => 'តែទឹកដោះគោជាមួយគ្រាប់គុជ ជ្រើសរើសកម្រិតផ្អែម។',
                'base_price' => 4.00,
                'category_id' => $drinksCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Medium', 'multiplier' => 1.0],
                    ['name' => 'Large', 'multiplier' => 1.25],
                ],
                'toppings' => [
                    ['name' => 'Boba Pearls', 'price' => 0.75],
                    ['name' => 'Jelly', 'price' => 0.75],
                    ['name' => 'Popping Boba', 'price' => 1.00],
                    ['name' => 'Aloe Vera', 'price' => 0.75],
                    ['name' => 'Grass Jelly', 'price' => 0.75],
                    ['name' => 'Pudding', 'price' => 0.75],
                ]
            ],
            [
                'name_km' => 'ស្មូធីម្នាស់',
                'name_en' => 'Pineapple Smoothie',
                'description' => 'ស្មូធីផ្លែម្នាស់រសជាតិឆ្ងាញ់និងសុខភាពល្អ។',
                'base_price' => 4.25,
                'category_id' => $drinksCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Medium', 'multiplier' => 1.0],
                    ['name' => 'Large', 'multiplier' => 1.30],
                ],
                'toppings' => [
                    ['name' => 'Honey', 'price' => 0.50],
                    ['name' => 'Aloe Vera', 'price' => 0.75],
                    ['name' => 'Jelly', 'price' => 0.75],
                ]
            ],
            [
                'name_km' => 'ទឹកក្រូច',
                'name_en' => 'Fresh Orange Juice',
                'description' => 'ទឹកក្រូចថ្មីៗច្របាច់ពីផ្លែក្រូចស្រស់។',
                'base_price' => 3.50,
                'category_id' => $drinksCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => false,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Medium', 'multiplier' => 1.0],
                    ['name' => 'Large', 'multiplier' => 1.40],
                ],
                'toppings' => []
            ],

            // Cake products
            [
                'name_km' => 'នំខេកសូកូឡា',
                'name_en' => 'Chocolate Cake',
                'description' => 'នំខេកសូកូឡាឆ្ងាញ់ដែលធ្វើអោយរលាយក្នុងមាត់។',
                'base_price' => 4.50,
                'category_id' => $cakeCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => false,
                'is_stock' => true,
                'quantity' => 20,
                'low_stock_threshold' => 5,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Slice', 'multiplier' => 1.0],
                    ['name' => 'Whole', 'multiplier' => 7.80],
                ],
                'toppings' => []
            ],
            [
                'name_km' => 'ឈីសខេក',
                'name_en' => 'Cheesecake',
                'description' => 'នំខេកឈីស New York Style ដែលមានរសជាតិឆ្ងាញ់និងដេនក្រអួន ក្រមៅ។',
                'base_price' => 5.00,
                'category_id' => $cakeCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => false,
                'is_stock' => true,
                'quantity' => 15,
                'low_stock_threshold' => 5,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Slice', 'multiplier' => 1.0],
                    ['name' => 'Whole', 'multiplier' => 8.0],
                ],
                'toppings' => []
            ],
            [
                'name_km' => 'តាតផ្លែស្ត្របឺរី',
                'name_en' => 'Strawberry Tart',
                'description' => 'តាតផ្លែស្ត្របឺរីស្រស់ៗ ដែលមានសុខភាពល្អ។',
                'base_price' => 4.00,
                'category_id' => $cakeCategory->id,
                'image' => null,
                'has_sizes' => false,
                'has_toppings' => false,
                'is_stock' => true,
                'quantity' => 10,
                'low_stock_threshold' => 3,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [],
                'toppings' => []
            ],

            // Pizza products
            [
                'name_km' => 'ភីហ្សាឈីស',
                'name_en' => 'Cheese Pizza',
                'description' => 'ភីហ្សាឈីសជាមួយសុសសាច់និងឈីសម៉ូហ្សារេឡា។',
                'base_price' => 8.00,
                'category_id' => $pizzaCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Small', 'multiplier' => 1.0],
                    ['name' => 'Medium', 'multiplier' => 1.5],
                    ['name' => 'Large', 'multiplier' => 2.0],
                    ['name' => 'Extra Large', 'multiplier' => 2.5],
                ],
                'toppings' => [
                    ['name' => 'Extra Cheese', 'price' => 1.50],
                    ['name' => 'Pepperoni', 'price' => 2.00],
                    ['name' => 'Mushrooms', 'price' => 1.50],
                    ['name' => 'Onions', 'price' => 1.00],
                    ['name' => 'Bell Peppers', 'price' => 1.00],
                    ['name' => 'Sausage', 'price' => 2.00],
                ]
            ],
            [
                'name_km' => 'ភីហ្សាបិពិរូនី',
                'name_en' => 'Pepperoni Pizza',
                'description' => 'ភីហ្សាដ៏កំសត់ដែលមានបិបបេរូនីសាឡាមីឆ្អិនល្អ។',
                'base_price' => 9.00,
                'category_id' => $pizzaCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Small', 'multiplier' => 1.0],
                    ['name' => 'Medium', 'multiplier' => 1.40],
                    ['name' => 'Large', 'multiplier' => 1.90],
                    ['name' => 'Extra Large', 'multiplier' => 2.30],
                ],
                'toppings' => [
                    ['name' => 'Extra Cheese', 'price' => 1.50],
                    ['name' => 'Extra Pepperoni', 'price' => 2.00],
                    ['name' => 'Mushrooms', 'price' => 1.50],
                    ['name' => 'Onions', 'price' => 1.00],
                    ['name' => 'Bell Peppers', 'price' => 1.00],
                    ['name' => 'Jalapeños', 'price' => 1.00],
                ]
            ],
            [
                'name_km' => 'ភីហ្សាបន្លែ',
                'name_en' => 'Veggie Pizza',
                'description' => 'ភីហ្សាខ្ទះផ្សែងបន្លែពណ៌សម្បូររសជាតិ។',
                'base_price' => 9.50,
                'category_id' => $pizzaCategory->id,
                'image' => null,
                'has_sizes' => true,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [
                    ['name' => 'Small', 'multiplier' => 1.0],
                    ['name' => 'Medium', 'multiplier' => 1.40],
                    ['name' => 'Large', 'multiplier' => 1.80],
                    ['name' => 'Extra Large', 'multiplier' => 2.20],
                ],
                'toppings' => [
                    ['name' => 'Extra Cheese', 'price' => 1.50],
                    ['name' => 'Mushrooms', 'price' => 1.50],
                    ['name' => 'Onions', 'price' => 1.00],
                    ['name' => 'Bell Peppers', 'price' => 1.00],
                    ['name' => 'Black Olives', 'price' => 1.00],
                    ['name' => 'Spinach', 'price' => 1.00],
                ]
            ],

            // Noodle products
            [
                'name_km' => 'រ៉ាមេន',
                'name_en' => 'Ramen',
                'description' => 'រ៉ាមេនទឹកស៊ុបឆ្ងាញ់ជាមួយស៊ុតជ្រូកនិងបន្លែស្រស់។',
                'base_price' => 8.50,
                'category_id' => $noodleCategory->id,
                'image' => null,
                'has_sizes' => false,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [],
                'toppings' => [
                    ['name' => 'Egg', 'price' => 1.00],
                    ['name' => 'Chashu Pork', 'price' => 2.50],
                    ['name' => 'Green Onions', 'price' => 0.50],
                    ['name' => 'Nori', 'price' => 0.75],
                    ['name' => 'Corn', 'price' => 0.75],
                    ['name' => 'Bean Sprouts', 'price' => 0.75],
                ]
            ],
            [
                'name_km' => 'មីឆា',
                'name_en' => 'Stir-fried Noodles',
                'description' => 'មីឆាជាមួយបន្លែនិងសាច់តាមជម្រើស។',
                'base_price' => 7.50,
                'category_id' => $noodleCategory->id,
                'image' => null,
                'has_sizes' => false,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [],
                'toppings' => [
                    ['name' => 'Egg', 'price' => 1.00],
                    ['name' => 'Seafood', 'price' => 3.00],
                    ['name' => 'Green Onions', 'price' => 0.50],
                    ['name' => 'Bean Sprouts', 'price' => 0.75],
                ]
            ],
            [
                'name_km' => 'មីហឹលសាឡាដ',
                'name_en' => 'Cold Noodle Salad',
                'description' => 'មីហឹលសាឡាដត្រជាក់ក្រអូបមេម៉ាយសម្រាប់រដូវក្ដៅ។',
                'base_price' => 6.50,
                'category_id' => $noodleCategory->id,
                'image' => null,
                'has_sizes' => false,
                'has_toppings' => true,
                'is_stock' => false,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'sizes' => [],
                'toppings' => [
                    ['name' => 'Egg', 'price' => 1.00],
                    ['name' => 'Chashu Pork', 'price' => 2.50],
                    ['name' => 'Seafood', 'price' => 3.00],
                    ['name' => 'Green Onions', 'price' => 0.50],
                    ['name' => 'Bean Sprouts', 'price' => 0.75],
                ]
            ],
        ];

        // No need to create Size and Topping records, they should be created by their respective seeders
        $this->command->info('Starting to create products...');

        // Create products with their sizes and toppings
        foreach ($products as $productData) {
            // Extract sizes and toppings
            $sizes = $productData['sizes'] ?? [];
            $toppings = $productData['toppings'] ?? [];

            // Remove sizes and toppings from product data
            unset($productData['sizes']);
            unset($productData['toppings']);

            // Create or update the product
            $product = Product::firstOrCreate(
                [
                    'name_km' => $productData['name_km'],
                    'name_en' => $productData['name_en'],
                ],
                $productData
            );

            // Create product sizes if this product has sizes
            if (!empty($sizes)) {
                $this->createProductSizes($product, $sizes, $adminId);
            }

            // Create product toppings if this product has toppings
            if (!empty($toppings)) {
                $this->createProductToppings($product, $toppings, $adminId);
            }
        }

        $this->command->info('Products have been seeded successfully!');
    }

    /**
     * Create product sizes for a product
     * Modified to use multiplier instead of price
     */
    protected function createProductSizes($product, $sizes, $adminId): void
    {
        foreach ($sizes as $sizeData) {
            // Get size by name
            $size = Size::where('name_en', $sizeData['name'])->first();

            if ($size) {
                // Check if the product size already exists using DB facade to avoid SoftDeletes query
                $exists = DB::table('product_sizes')
                    ->where('product_id', $product->id)
                    ->where('size_id', $size->id)
                    ->exists();

                if (!$exists) {
                    // Insert directly using DB facade to avoid SoftDeletes issue
                    DB::table('product_sizes')->insert([
                        'product_id' => $product->id,
                        'size_id' => $size->id,
                        'multiplier' => $sizeData['multiplier'],
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Create product toppings for a product
     * Modified to avoid SoftDeletes issue and remove is_active field
     */
    protected function createProductToppings($product, $toppings, $adminId): void
    {
        foreach ($toppings as $toppingData) {
            // Get topping by name
            $topping = Topping::where('name_en', $toppingData['name'])->first();

            if ($topping) {
                // Check if the product topping already exists using DB facade to avoid SoftDeletes query
                $exists = DB::table('product_toppings')
                    ->where('product_id', $product->id)
                    ->where('topping_id', $topping->id)
                    ->exists();

                if (!$exists) {
                    // Insert directly using DB facade - removed is_active field
                    DB::table('product_toppings')->insert([
                        'product_id' => $product->id,
                        'topping_id' => $topping->id,
                        'price' => $toppingData['price'],
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
