<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;
class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::create([
            'key' => 'currency',
            'value' => 'USD',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
