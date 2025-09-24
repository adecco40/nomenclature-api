<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Faker\Factory as Faker;
use App\Models\User;


class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $categories = Category::pluck('id')->toArray();
        $suppliers = Supplier::pluck('id')->toArray();
        $admin = User::where('email', 'admin@example.com')->first();


        for ($i = 0; $i < 5000; $i++) {
            Product::create([
                'name' => $faker->word,
                'description' => $faker->sentence,
                'category_id' => $faker->randomElement($categories),
                'supplier_id' => $faker->randomElement($suppliers),
                'price' => $faker->randomFloat(2, 10, 1000),
                'is_active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }
    }
}