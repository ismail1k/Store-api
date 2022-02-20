<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ContactSeeder::class,
            InventorySeeder::class,
            SkuSeeder::class,
            MediaSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
