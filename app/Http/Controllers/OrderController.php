<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use Cart;
use Auth;
use DB;

class OrderController extends Controller
{
    public static function get($order_id){
        if($order = Order::whereId($order_id)->first()){
            dd($order->items);
            $cart = [
                'id' => Cart::get($order->cart_id)->id,
                'items' => [],
            ];
            foreach(Cart::get($order->cart_id)->items as $item){
                $product = Product::whereId($item->id)->first();
                array_push($cart['items'], [
                    'id' => $item->id,
                    'name' => $product->name,
                    'description' => $product->short_description,
                    'type' => $product->inventory->digital?'Digital': 'Physical',
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price * $item->quantity,
                ]);
            }
            return [
                'id' => $order->id,
                'cart' => $cart,
                'user' => $order->user_id?User::whereId($order->user_id)->first():null,
                'payment_method' => $order->payment_method,
                'fullname' => $order->fullname,
                'address' => $order->address,
                'phone' => $order->phone,
                'state' => $order->state,
                'note' => $order->note,
            ];
        }
        return false;
    }

    public function all(){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'order_all')){
                $response = [];
                foreach(Order::all() as $order){
                    array_push($response, self::get($order->id));
                }
                return response()->json($response);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function view(Request $request){
        if($order = self::get($request['order_id'])){
            if($order['user'] == null){
                return response()->json($order);
            }
            if(Auth::check()){
                if($order['user']['id'] == Auth::user()->id){
                    return response()->json($order);
                }
                if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'order_view')){
                    return response()->json($order);
                }
            }
        }
        return response()->json(['status' => 404]);
    }

    public function edit(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'order_edit')){
                if(Order::whereId($request['order_id'])->first()){
                    Order::whereId($request['order_id'])->update($request->except('token', 'order_id', 'cart_id', 'payment_method'));
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function new(Request $request){
        $user = Auth::check()?User::whereId(Auth::user()->id)->first():null;
        if(strlen($request['phone']) < 8 || strlen($request['phone']) > 13){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!$request['fullname']){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!$request['email']){
            return response()->json(['status' => 500, 'message' => 'Bad email']);
        }
        if(!$request['address']){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!count(Cart::get($request['cart_id'])->items)){
            return response()->json(['status' => 500, 'message' => 'Empty cart']);
        }
        // dd(Product::whereId(1)->first()->inventory->items);
        $order_id = Order::create([
            'user_id' => $user?$user->id:null,
            'cart_id' => $request['cart_id'],
            'fullname' => $request['fullname'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'note' => $request['note'],
        ])->id;
        return response()->json([
            'status' => 200,
            'order_id' => $order_id,
        ]);
    }

    public function remove(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'order_remove')){
                if(Order::whereId($request['order_id'])->first()){
                    Order::whereId($request['order_id'])->delete();
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public static function confirm($order_id){
        $cart = Cart::get($order_id);
        $order = Order::whereId($order_id)->first();
        dd($cart);
        foreach($order->items as $item){
            OrderItems::create([
                'order_id' => $order_id,
                'product_id' => $item->id,
                'quantity' => $item->quantity,
                'payed' => true,
            ]);
            Cart::removeItem($item->item_id);
        }
        return false;
    }
}
