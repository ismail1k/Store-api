<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;


class CodPaymentProcessor extends Controller
{
    public function order($order_id = null){
        if($order = Order::whereId($order_id)->first()){
            $items = [];
            $total = 0;
            foreach(Cart::findById($order->cart_id)->getCart()->items as $item){
                if($product = Product::whereId($item->purchaseable_id)->first()){
                    $total += $product->price-$product->discount;
                    array_push($items, (object)[
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->short_description,
                        'type' => $product->inventory->digital?'Digital': 'Physical',
                        'price' => $product->price-$product->discount,
                        'quantity' => $item->qty,
                    ]);
                }
            }
            $prototype = [
                'id' => $order->id,
                'payment_method' => $order->payment_method,
                'total' => $total,
                'items' => $items,
            ];
            return (object)$prototype;
        }
        return false;
    }

    public function init(Request $request){
        
        return true;
    }
}
