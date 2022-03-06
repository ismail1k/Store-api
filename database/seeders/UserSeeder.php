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
            'name' => 'Owner Account',
            'role' => '3',
            'phone' => '0612345678',
            'email' => 'owner@store',
            'password' => Hash::make('password'),
        ]);
        User::create([
            'name' => 'Staff Account',
            'role' => '2',
            'phone' => '0612345678',
            'email' => 'staff@store',
            'password' => Hash::make('password'),
        ]);
        User::create([
            'name' => 'Customer Account',
            'role' => '1',
            'phone' => '0612345678',
            'email' => 'customer@store',
            'password' => Hash::make('password'),
        ]);
    }
}
