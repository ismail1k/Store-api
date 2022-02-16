<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Hash;
use DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'avatar' => 'team-4.jpg',
            'name' => 'Ismail Andaloussi',
            'role' => '3',
            'email' => 'admin@store',
            'password' => Hash::make('password'),
        ]);
    }
}
