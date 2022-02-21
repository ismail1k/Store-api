<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Auth;

class DataController extends Controller
{
    public function menu(){
        if(Auth::check()){
            if(Auth::user()->role >= 2){
                $menu = [
                    'dashboard' => [
                        'name' => 'Dashboard',
                        'icon' => 'tachometer-alt',
                        'url' => 'dashboard',
                        'permissions' => ['dashboard_view'],
                    ],
                    'product' => [
                        'name' => 'Product',
                        'icon' => 'tachometer-alt',
                        'url' => 'product',
                        'permissions' => ['product_view', 'product_new', 'product_edit', 'product_remove'],
                    ],
                    'category' => [
                        'name' => 'Category',
                        'icon' => 'tachometer-alt',
                        'url' => 'category',
                        'permissions' => ['category_view', 'category_new', 'category_edit', 'category_remove'],
                    ],
                    'inventory' => [
                        'name' => 'Inventory',
                        'icon' => 'tachometer-alt',
                        'url' => 'inventory',
                        'permissions' => ['inventory_view', 'inventory_new', 'inventory_edit', 'inventory_remove'],
                    ],
                    'customer' => [
                        'name' => 'Customer',
                        'icon' => 'tachometer-alt',
                        'url' => 'customer',
                        'permissions' => ['customer_view', 'customer_new', 'customer_edit', 'customer_remove'],
                    ],

                ];
                $response = [];
                foreach($menu as $m){
                    $permissions = [];
                    foreach($m['permissions'] as $permission){
                        if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, $permission)){
                            array_push($permissions, $permission);
                        }
                    }
                    if(count($permissions)){
                        array_push($response, [
                            'name' => $m['name'],
                            'icon' => $m['icon'],
                            'url' => $m['url'],
                            'permission' => $permissions,
                        ]);
                    }
                }
                return response()->json($response);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
