<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Cart;
use Wishlist;
use Auth;

class CartController extends Controller
{
    public function view(Request $request){
        if(Cart::get($request['cart_id'])){
            $cart = [
                'id' => Cart::get($request['cart_id'])->id,
                'items' => [],
            ];
            foreach(Cart::get($request['cart_id'])->items as $item){
                $product = Product::whereId($item->id)->first();
                array_push($cart['items'], [
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                ]+(array)ProductController::get($product->id));
            }
            return response()->json($cart);
        }
        return response()->json(['status' => 404]);
    }

    public function create(Request $request){
        return response()->json([
            'cart_id' => Cart::create(Auth::check()?Auth::user()->id:null),
        ]);
    }

    public function addToCart(Request $request){
        if($product = Product::whereId($request['product_id'])->where('available', true)->first()){
            if($cart = Cart::get($request['cart_id'])){
                if($product->inventory->quantity >= $request['quantity']){
                    Cart::add($cart->id, $product->id, $request['quantity']);
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 500, 'message' => 'Unavailable Quantity']);
            }
        }
        return response()->json(['status' => 404]);
    }

    public function removeFromCart(Request $request){
        if(Cart::get($request['cart_id'])){
            Cart::removeItem($request['item_id']);
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 404]);
    }

    public function clear(Request $request){
        if($cart = Cart::get($request['cart_id'])){
            Cart::clear($cart->id);
        }
        return response()->json(['status' => 200]);
    }
}
