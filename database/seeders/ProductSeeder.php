<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'D\'Urbano Baskets Stretch et cuir - Blanc.',
            'short_description' => 'Avec cette basket, D’Urbano se positionne en partenaire urbaine de tout citadin. Facile à vivre avec sa tige en cuir et son coloris Blanc, elle conjugue confort et style. Une semelle souple et une doublure en synthétique contribuent au succès de ce phénomène. Entre ses détails sport et sa ligne racée, elle a tout pour plaire aux citadins !',
            'description' => '<strong>Desc:&nbsp;</strong>Avec cette basket, D’Urbano se positionne en partenaire urbaine de tout citadin. Facile à vivre avec sa tige en cuir et son coloris Blanc, elle conjugue confort et style. Une semelle souple et une doublure en synthétique contribuent au succès de ce phénomène. Entre ses détails sport et sa ligne racée, elle a tout pour plaire aux citadins !',
            'tags' => 'digital, sport, news, gaming, blog',
            'media_id' => 1,
            'category_id' => 1,
            'inventory_id' => 1,
            'price' => 499.00,
            'discount' => 270.00,
            'virtual' => false,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
