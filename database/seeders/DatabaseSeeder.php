<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // First create the user
            UserSeeder::class,
            PermissionRoleSeeder::class,
            CategorySeeder::class,
            SizeSeeder::class,
            ToppingSeeder::class,
            ProductSeeder::class,
            CurrencySeeder::class,
            DiscountSeeder::class,
            PaymentMethodSeeder::class,
        ]);
    }
}
