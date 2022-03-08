<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Auth;

class OrderController extends Controller
{
    private static function get($order_id){
        if($order = Order::whereId($order_id)->first()){
            return [
                'id' => $order->id,
                'product' => ProductController::get($order->product_id),
                'user' => $order->user_id?User::whereId($order->user_id):null,
                'quantity' => $order->quantity,
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
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'order_view')){
                if($order = self::get($request['order_id'])){
                    return response()->json($order);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
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
        $product = Product::whereId($request['product_id'])->first();
        $user = User::whereId($request['user_id'])->first();
        $quantity = $request['quantity'];
        $fullname = $request['fullname'];
        $payment_method = $request['payment_method'];
        $address = $request['address'];
        $phone = $request['phone'];
        $note = $request['note'];
        if(!$product->available){
            return response()->json(['status' => 404]);
        }
        if(!$payment_method){
            return response()->json(['status' => 500, 'message' => 'Bad Payment Method']);
        }
        if(strlen($phone) < 8 || strlen($phone) > 13){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!$fullname){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(!$address){
            return response()->json(['status' => 500, 'message' => 'Bad info']);
        }
        if(($product->inventory->quantity < $quantity) || !$quantity){
            return response()->json(['status' => 500, 'message' => 'Bad Quantity']);
        }
        Inventory::whereId($product->inventory->id)->update([
            'quantity' => $product->inventory->quantity - $quantity,
        ]);
        $order_id = Order::create([
            'product_id' => $product->id,
            'user_id' => $user?$user->id:null,
            'fullname' => $fullname,
            'address' => $address,
            'phone' => $phone,
            'payment_method' => $payment_method,
            'quantity' => $quantity,
            'note' => $note,
        ])->id;
        return response()->json(['status' => 200, 'order_id' => $order_id]);
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
}
