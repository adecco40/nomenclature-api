<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Faker\Factory as Faker;
use App\Models\User;


class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $admin = User::where('email', 'admin@example.com')->first();

        // Создаём 10 корневых категорий
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = Category::create([
                'name' => $faker->word,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                
            ]);
        }

        // Для каждой корневой создаём 2-4 подкатегории
        foreach ($categories as $parent) {
            for ($i = 0; $i < rand(2, 4); $i++) {
                Category::create([
                    'name' => $faker->word,
                    'parent_id' => $parent->id,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]);
            }
        }
    }
}