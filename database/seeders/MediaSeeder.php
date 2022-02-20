<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Media;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Media::create([
            'path' => '2022-02-02 12-32-27 40806.jpg',
            'product_id' => 1,
            'primary' => true,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        Media::create([
            'path' => '2022-02-02 12-33-04 73918.jpg',
            'product_id' => 1,
            'primary' => false,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        Media::create([
            'path' => '2022-02-02 12-33-49 71639.jpg',
            'product_id' => 1,
            'primary' => false,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        Media::create([
            'path' => '2022-02-02 12-33-58 26861.jpg',
            'product_id' => 1,
            'primary' => false,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        Media::create([
            'path' => '2022-02-02 14-05-16 50174.mp4',
            'product_id' => 1,
            'primary' => false,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
