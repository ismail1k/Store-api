<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Yab\ShoppingCart\Checkout;
use Auth;

class CartController extends Controller
{
    public function all(Request $request){
        $cart = Checkout::findById((string)$request['cart']);
        if($cart){
            return response()->json($cart->getCart());
        }
        return response()->json(['status' => 404]);
    }

    public function create(Request $request){
        $cart = Checkout::create();
        return response()->json(['cart' => $cart->id()]);
    }

    public function addToCart(Request $request){
        $product_id = lib::filter($request['product_id']);
        $quantity = lib::filter($request['quantity']);
        $product = Product::whereId($product_id)->first();
        if($product){
            $cart = Checkout::findById((string)$request['cart']);
            if($cart){
                $cart->addItem($product, $quantity, $product->price-$product->discount);
                return response()->json(['status' => 200]);
            }
        }
        return response()->json(['status' => 404]);
    }

    public function removeFromCart(Request $request){
        $cart = Checkout::findById($request['cart']);
        if($cart){
            $cart->removeItem($request['item_id']);
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 404]);
    }

    public function clear(Request $request){
        @Checkout::findById((string)$request['cart'])->destroy();
        return response()->json(['status' => 200]);
    }
}
