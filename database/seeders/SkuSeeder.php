<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sku;

class SkuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sku::create([
            'value' => '',
            'valid' => 1,
            'inventory_id' => 1,
        ]);
        Sku::create([
            'value' => '',
            'valid' => 1,
            'inventory_id' => 1,
        ]);
    }
}
