<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Yab\ShoppingCart\Checkout;
use App\Models\Product;
use App\Models\Order;


class PaypalPaymentProcessor extends Controller
{
    private static function onSuccess($order_id){
        Order::whereId($order_id)->update(['payment_method' => 'paypal']);
        $cart = Checkout::findById($order_id)->getCart();
        foreach($cart->items as $item){
            $product = Product::whereId($item->purchaseable_id)->first();
            $inventory = Inventory::whereId($product->inventory->id)->first();
            Inventory::whereId($inventory->id)->update(['quantity' => $inventory->quantity-$item->qty]);
            if($inventory->digital == false){
                //Physical Inventory

            }
            if($inventory->digital == true){
                //Digital Inventory 

            }
        }
        return false;
    }

    private static function order($order_id){
        if($order = Order::whereId($order_id)->first()){
            $items = [];
            $total = 0;
            foreach(Checkout::findById($order->cart_id)->getCart()->items as $item){
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

    public static function init(Request $request){
        if($order = self::order($request['order_id'])){
            if($order->payment_method){
                return response()->json(['status' => 200, 'message' => 'Already paid']);
            }
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setAccessToken($provider->getAccessToken());

            $data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [],
                'application_context' => [
                    'return_url' => route('payment.paypal.return'),
                    'cancel_url' => route('payment.paypal.cancel'),
                ]
            ];
            foreach($order->items as $item){
                array_push($data['purchase_units'], [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $item->price*$item->quantity,
                    ],
                ]);
            }
            $response = $provider->createOrder($data);
            if($response['status'] == 'CREATED'){
                foreach($response['links'] as $link){
                    if($link['rel'] == 'approve'){
                        $url = $link['href'];
                    }
                }
                if($url){
                    Order::whereId($order->id)->update([
                        'transaction_id' => $response['id']
                    ]);
                }
            } else {
                return response()->json(['status' => 500]);
            }
            return response()->json([
                'status' => 200,
                'url' => $url,
            ]);
        }
        return response()->json(['status' => 404]);
    }

    public function return(Request $request){
        if($order = Order::where('transaction_id', $request['token'])->first()){
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setAccessToken($provider->getAccessToken());
            $response = $provider->capturePaymentOrder($request['token']);
            if(isset($response['status']) && $response['status'] == 'COMPLETED'){
                self::onSuccess($order->id);
                return response()->json([
                    'status' => 200,
                ]);
            }
            return response()->json([
                'status' => 500,
            ]);
        }
        return response()->json(['status' => 404]);
    }

    public function cancel(){
        return response()->json(['status' => 200]);
    }
}