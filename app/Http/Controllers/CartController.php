<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Auth;

class CartController extends Controller
{
    public function all(Request $request){
        if(Checkout::findById((string)$request['cart'])){
            $cart = [
                'id' => Checkout::findById((string)$request['cart'])->getCart()->id,
                'items' => [],
            ];
            foreach(Checkout::findById((string)$request['cart'])->getCart()->items as $item){
                $product = Product::whereId($item->purchaseable_id)->first();
                array_push($cart['items'], [
                    'item_id' => $item->id,
                    'unit_price' => $item->unit_price,
                    'price' => $item->price,
                    'quantity' => $item->qty
                ]+(array)ProductController::get($product->id));
            }
            return response()->json($cart);
        }
        return response()->json(['status' => 404]);
    }

    public function create(Request $request){
        return response()->json([
            'Cart' => Cart::get(),
            'Wishlist' => Wishlist::get(),
        ]);
    }

    public function addToCart(Request $request){
        $product_id = lib::filter($request['product_id']);
        $quantity = lib::filter($request['quantity']);
        $product = Product::whereId($product_id)->first();
        if($product){
            $cart = Checkout::findById((string)$request['cart']);
            if($cart){
                if($product->inventory->quantity < $quantity){
                    return response()->json(['status' => 500, 'message' => 'Unavailable Quantity']);
                }
                $cart->addItem($product, $quantity, $product->price-$product->discount);
                return response()->json(['status' => 200]);
            }
        }
        return response()->json(['status' => 404]);
    }

    public function removeFromCart(Request $request){
        if($cart = Checkout::findById($request['cart'])){
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
