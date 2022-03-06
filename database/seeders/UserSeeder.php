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
            'name' => 'Default Account',
            'role' => '3',
            'phone' => '0612345678',
            'email' => 'admin@store',
            'password' => Hash::make('password'),
        ]);
    }
}
