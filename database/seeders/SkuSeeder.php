<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sku;
use Str;

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
            'value' => strtoupper(Str::random(5).'-'.Str::random(5).'-'.Str::random(5)),
            'valid' => true,
            'inventory_id' => 1,
        ]);
        Sku::create([
            'value' => strtoupper(Str::random(5).'-'.Str::random(5).'-'.Str::random(5)),
            'valid' => true,
            'inventory_id' => 1,
        ]);
    }
}
