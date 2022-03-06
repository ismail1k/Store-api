<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(\App\Http\Controllers\MenuController::get() as $key => $value){
            Permission::create(['name' => $key]);
        }
        \App\Models\User::whereId(2)->first()->givePermissionTo('product');
        \App\Models\User::whereId(2)->first()->givePermissionTo('order');
    }
}