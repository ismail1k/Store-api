<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sku;
use App\Models\Order;
use App\Models\OrderItems;
use Cart;


class PaypalPaymentProcessor extends Controller
{
    private static function order($order_id){
        if($order = Order::whereId($order_id)->first()){
            $items = [];
            $total = 0;
            foreach(Cart::get($order->cart_id)->items as $item){
                if($product = Product::whereId($item->id)->first()){
                    $total += $product->price-$product->discount;
                    array_push($items, (object)[
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->short_description,
                        'type' => $product->inventory->digital?'Digital': 'Physical',
                        'price' => $product->price-$product->discount,
                        'quantity' => $item->quantity,
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
        if($order = Order::whereId($request['order_id'])->first()){
            if($order->payed){
                return response()->json(['status' => 200, 'message' => 'Already paid']);
            }
            if(!count($order->items)){
                return response()->json(['status' => 500, 'message' => 'Empty cart']);
            }
            foreach($order->items as $i){
                $product = Product::whereId($i->product_id)->first();
                if(!$product->available){
                    return response()->json(['status' => 500, 'message' => 'Unavailable Product']);
                }
                if($i->quantity > $product->inventory->quantity){
                    return response()->json([
                        'status' => 500,
                        'message' => 'Unavailable Quantity',
                    ]);
                }
            }

            $data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [],
                'application_context' => [
                    'return_url' => route('payment.paypal.return'),
                    'cancel_url' => route('payment.paypal.cancel'),
                ]
            ];
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setAccessToken($provider->getAccessToken());
            $amount = 0;
            foreach($order->items as $i){
                $amount += ($product->price-$product->discount)*$product->quantity;
                $product = Product::whereId($i->product_id)->first();
                array_push($data['purchase_units'], [
                    'reference_id' => $product->id,
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => ($product->price-$product->discount)*$product->quantity,
                    ],
                ]);
            }
            $response = $provider->createOrder($data);
            dd($response);
            if($response['status'] == 'CREATED'){
                foreach($response['links'] as $link){
                    if($link['rel'] == 'approve'){
                        $url = $link['href'];
                    }
                }
                if($url){
                    Payment::create([
                        'order_id' => $order->id,
                        'reference' => $response['id'],
                        'amount' => $amount,
                        'provider' => 'PayPal',
                        'status' => 1,
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
        $payment = Payment::where('reference', $request['token'])->first();
        $order = $payment->order;
        if($order){
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setAccessToken($provider->getAccessToken());
            $response = $provider->capturePaymentOrder($request['token']);
            if(isset($response['status']) && $response['status'] == 'COMPLETED'){
                Order::whereId($order->id)->update(['payed' => true]);
                foreach($order->items as $item){
                    $product = Product::whereId($item->product_id)->first();
                    Inventory::whereId($product->inventory->id)->update(['quantity' => $product->inventory->quantity-$item->quantity]);
                    if($product->inventory->digital == true){
                        foreach(Sku::where('inventory_id', $product->inventory->id)->where('valid', true)->get()->take($item->quantity) as $key){
                            Sku::whereId($key->id)->update(['valid' => false]);
                        }
                    }
                }
                return view('close');
            }
            return response()->json([
                'status' => 500,
            ]);
        }
        return response()->json(['status' => 404]);
    }

    public function execute(Request $request){
        if($order = Order::where('transaction_id', $request['token'])->first()){
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setAccessToken($provider->getAccessToken());
            $response = $provider->capturePaymentOrder($request['token']);
            if(isset($response['status']) && $response['status'] == 'COMPLETED'){
                $order = Order::where('transaction_id', $response['id'])->first();
                $cart = Checkout::findById($order->cart_id);
                Order::where('transaction_id', $response['id'])->update(['payment_method' => 'paypal']);
                $keys = [];
                foreach($cart->getCart()->items as $item){
                    $product = Product::whereId($item->purchaseable_id)->first();
                    $inventory = Inventory::whereId($product->inventory->id)->first();
                    Inventory::whereId($inventory->id)->update(['quantity' => $inventory->quantity-$item->qty]);
                    if($inventory->digital == true){
                        foreach(Sku::where('inventory_id', $inventory->id)->where('valid', true)->get()->take($item->qty) as $key){
                            Sku::whereId($key->id)->update(['valid' => false]);
                            array_push($keys, [
                                'name' => $product->name,
                                'key' => $key->value,
                            ]);
                        }
                    }
                    $cart->removeItem($item->id);
                }
                return response()->json([
                    'status' => 200,
                    'keys' => $keys
                ]);
            }
            return response()->json([
                'status' => 500,
            ]);
        }
        return response()->json(['status' => 404]);
    }

    public function cancel(){
        return view('close');
    }
}