<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Ismail Andaloussi',
            'role' => '3',
            'email' => 'admin@store',
            'password' => Hash::make('password'),
        ]);
    }
}
