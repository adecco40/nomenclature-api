<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\User;
use Faker\Factory as Faker;


class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $admin = User::where('email', 'admin@example.com')->first();


        for ($i = 0; $i < 100; $i++) {
            Supplier::create([
                'name' => $faker->company,
                'phone' => $faker->phoneNumber,
                'contact_name' => $faker->name,
                'website' => $faker->url,
                'description' => $faker->sentence,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }
    }
}