<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class MenuController extends Controller
{
    public static function get(){
        $menu = [
            'dashboard' => [
                'name' => 'Dashboard',
                'icon' => 'tachometer-alt',
                'url' => 'dashboard',
                'active' => true,
            ],
            'order' => [
                'name' => 'Order',
                'icon' => 'cart-shopping',
                'url' => 'order',
                'active' => true,
            ],
            'product' => [
                'name' => 'Product',
                'icon' => 'box-open',
                'url' => 'product',
                'active' => true,
            ],
            'inventory' => [
                'name' => 'Inventory',
                'icon' => 'boxes-stacked',
                'url' => 'inventory',
                'active' => true,
            ],
            'integration' => [
                'name' => 'Integration',
                'icon' => 'tachometer-alt',
                'url' => 'integration',
                'active' => false,
            ],
            'category' => [
                'name' => 'Category',
                'icon' => 'list-ul',
                'url' => 'category',
                'active' => true,
            ],
            'contact' => [
                'name' => 'Contact',
                'icon' => 'envelope',
                'url' => 'contact',
                'active' => true,
            ],
            'customer' => [
                'name' => 'Customer',
                'icon' => 'users',
                'url' => 'customer',
                'active' => true,
            ],
            'settings' => [
                'name' => 'Settings',
                'icon' => 'wrench',
                'url' => 'settings',
                'active' => true,
            ],

        ];
        
        $response = [];
        foreach($menu as $key => $value){
            if($value['active']){
                $response[$key] = $value;
            }
        }
        return json_decode(json_encode($response));
    }

    public static function allowed($user_id = null){
        if(!$user_id){
            $user_id = Auth::user()->id;
        }
        return $user_id;
    }
}
