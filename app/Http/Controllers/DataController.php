<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Auth;

class DataController extends Controller
{
    public function menu(){
        if(Auth::check()){
            if(Auth::user()->role >= 2){
                $menu = [
                    // 'dashboard' => [
                    //     'active' => true,
                    //     'name' => 'Dashboard',
                    //     'icon' => 'tachometer-alt',
                    //     'url' => 'dashboard',
                    //     'permissions' => ['dashboard_view'],
                    // ],
                    'order' => [
                        'active' => true,
                        'name' => 'Order',
                        'icon' => 'cart-shopping',
                        'url' => 'order',
                        'permissions' => ['order_view', 'order_new', 'order_edit', 'order_remove'],
                    ],
                    'product' => [
                        'active' => true,
                        'name' => 'Product',
                        'icon' => 'box-open',
                        'url' => 'product',
                        'permissions' => ['product_view', 'product_new', 'product_edit', 'product_remove'],
                    ],
                    'inventory' => [
                        'active' => true,
                        'name' => 'Inventory',
                        'icon' => 'boxes-stacked',
                        'url' => 'inventory',
                        'permissions' => ['inventory_view', 'inventory_new', 'inventory_edit', 'inventory_remove'],
                    ],
                    'integration' => [
                        'active' => false,
                        'name' => 'Integration',
                        'icon' => 'tachometer-alt',
                        'url' => 'integration',
                        'permissions' => ['integration_view', 'integration_new', 'integration_edit', 'integration_remove'],
                    ],
                    'category' => [
                        'active' => true,
                        'name' => 'Category',
                        'icon' => 'list-ul',
                        'url' => 'category',
                        'permissions' => ['category_view', 'category_new', 'category_edit', 'category_remove'],
                    ],
                    'customer' => [
                        'active' => true,
                        'name' => 'Customer',
                        'icon' => 'users',
                        'url' => 'customer',
                        'permissions' => ['customer_view', 'customer_new', 'customer_edit', 'customer_remove'],
                    ],
                    'contact' => [
                        'active' => true,
                        'name' => 'Contact',
                        'icon' => 'envelope',
                        'url' => 'contact',
                        'permissions' => ['contact_view', 'contact_new', 'contact_edit', 'contact_remove'],
                    ],
                    'settings' => [
                        'active' => false,
                        'name' => 'Settings',
                        'icon' => 'wrench',
                        'url' => 'settings',
                        'permissions' => ['settings_view', 'settings_new', 'settings_edit', 'settings_remove'],
                    ],

                ];
                $response = [];
                foreach($menu as $m){
                    if(!$m['active']){
                        continue;
                    }
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
