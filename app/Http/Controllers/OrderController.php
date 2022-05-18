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

class OrderController extends Controller
{
    public static function get($order_id){
        if($order = Order::whereId($order_id)->first()){
            $items = [];
            foreach($order->items as $item){
                $product = Product::whereId($item->product_id)->first();
                array_push($items, (object)[
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->short_description,
                    'type' => $product->inventory->digital?'Digital': 'Physical',
                    'price' => $product->price-$product->discount,
                    'quantity' => $item->quantity,
                ]);
            }
            return (object)[
                'id' => $order->id,
                'user' => $order->user_id?User::whereId($order->user_id)->first():null,
                'fullname' => $order->fullname,
                'address' => $order->address,
                'phone' => $order->phone,
                'note' => $order->note,
                'state' => $order->state,
                'payed' => $order->payed,
                'items' => $items,
                'payment' => $order->payment,
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
            if($order->user == null){
                return response()->json($order);
            }
            if(Auth::check()){
                if($order->user->id == Auth::user()->id){
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
                    Order::whereId($request['order_id'])->update($request->except('token', 'order_id'));
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
        $fullname = $request['fullname'];
        $address = $request['address'];
        $phone = $request['phone'];
        $note = $request['note'];
        if(strlen($phone) < 8 || strlen($phone) > 13){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!$fullname){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!$address){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!count(Cart::get($request['cart_id'])->items)){
            return response()->json(['status' => 500, 'message' => 'Empty cart']);
        }
        
        $order_id = Order::create([
            'user_id' => $user?$user->id:null,
            'fullname' => $fullname,
            'phone' => $phone,
            'address' => $address,
            'note' => $note,
        ])->id;
        foreach(Cart::get($request['cart_id'])->items as $item){
            $product = Product::whereId($item->id)->first();
            if($product->available && ($product->inventory->quantity >= $item->quantity)){
                OrderItems::create([
                    'order_id' => $order_id,
                    'product_id' => $item->id,
                    'quantity' => $item->quantity,
                ]);
            }
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
                    OrderItems::where('order_id', $request['order_id'])->delete();
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
        if($order = Order::whereId($order_id)->first()){
            foreach($order->items as $item){
                $product = Product::whereId($item->product_id)->first();
                Inventory::whereId($product->inventory->id)->update([
                    'quantity' => $product->inventory->quantity-$item->quantity,
                ]);
            }
            return true;
        }
        return false;
    }
}
