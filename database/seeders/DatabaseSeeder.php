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
            ConfigSeeder::class,
            InventorySeeder::class,
            MediaSeeder::class,
            ProductSeeder::class,
            PermissionSeeder::class,
            // OrderSeeder::class,
        ]);
    }
}
