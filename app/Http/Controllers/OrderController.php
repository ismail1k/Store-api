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
            $items = [];
            foreach($order->items as $item){
                $product = Product::whereId($item->product_id)->first();
                array_push($items, [
                    'id' => $item->id,
                    'name' => $product->name,
                    'description' => $product->short_description,
                    'type' => $product->inventory->digital?'Digital': 'Physical',
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                ]);
            }
            return [
                'id' => $order->id,
                'items' => $items,
                'user' => $order->user_id?User::whereId($order->user_id)->first():null,
                'payment' => $order->payment,
                'fullname' => $order->fullname,
                'address' => $order->address,
                'email' => $order->email,
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
        $order_id = Order::create([
            'user_id' => $user?$user->id:null,
            'cart_id' => $request['cart_id'],
            'fullname' => $request['fullname'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'note' => $request['note'],
        ])->id;
        foreach(Cart::get($request['cart_id'])->items as $item){
            OrderItems::create([
                'order_id' => $order_id,
                'product_id' => $item->id,
                'item_id' => $item->item_id,
                'quantity' => $item->quantity,
                'payed' => false,
            ]);
            // Cart::removeItem($item->item_id);
        }
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
        $order = Order::whereId($order_id)->first();
        foreach($order->items as $item){
            OrderItems::whereId($item->id)->update([
                'payed' => true,
            ]);
            Cart::removeItem($item->item_id);
        }
        return true;
    }
}
