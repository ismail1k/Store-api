<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventory_id = Inventory::create(['name' => 'D\'Urbano Baskets Stretch et cuir - Blanc.\'s Inventory'])->id;
        DB::table('sku')->insertGetId([
            'inventory_id' => $inventory_id,
            'value' => '',
            'valid' => 1,
        ]);
        DB::table('sku')->insertGetId([
            'inventory_id' => $inventory_id,
            'value' => '',
            'valid' => 1,
        ]);
    }
}
