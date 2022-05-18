<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function cash_on_delivery(Request $request){
        if($order = Order::whereId($request['order_id'])->first()){
            $amount = 0;
            foreach($order->items as $item){
                $product = Product::whereId($item->product_id)->first();
                if(!$product->available || ($product->inventory->quantity < $item->quantity)){
                    OrderItems::whereId($item->id)->delete();
                } else {
                    $amount += ($product->price-$product->discount)*$item->quantity;
                }
            }
            Payment::create([
                'order_id' => $order->id,
                'amount' => $amount,
                'provider' => 'CashOnDelivery',
            ]);
            Order::whereId($order->id)->update([
                'payed' => true
            ]);
            OrderController::confirm($order->id);
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 404]);
    }
    
    public function paypal(Request $request){

        return response()->json(500);
    }
    
    public function credit_cart(Request $request){

        return response()->json(500);
    }
}
