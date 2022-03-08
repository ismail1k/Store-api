<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Inventory::create([
            'name' => 'D\'Urbano Baskets Stretch et cuir - Blanc.\'s Inventory',
            'quantity' => 5,
            'digital' => 0,
        ]);
    }
}
