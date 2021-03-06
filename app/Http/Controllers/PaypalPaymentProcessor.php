<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sku;
use App\Models\Order;
<<<<<<< HEAD
use App\Models\Payment;
=======
use App\Models\OrderItems;
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
use Cart;
use DB;

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
<<<<<<< HEAD
                    $payment_id = Payment::create([
                        'reference' => $response['id'],
                        'provider' => 'PayPal',
                    ])->id;
                    Order::whereId($order->id)->update([
                        'payment_id' => $payment_id,
=======
                    Payment::create([
                        'order_id' => $order->id,
                        'reference' => $response['id'],
                        'amount' => $amount,
                        'provider' => 'PayPal',
                        'status' => 1,
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
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
<<<<<<< HEAD
        $payment_id = Payment::where('reference', $request['token'])->first()->id;
        if($order = Order::where('id', $payment_id)->first()){
=======
        $payment = Payment::where('reference', $request['token'])->first();
        $order = $payment->order;
        if($order){
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setAccessToken($provider->getAccessToken());
            $response = $provider->capturePaymentOrder($request['token']);
            
            if(isset($response['status']) && $response['status'] == 'COMPLETED'){
<<<<<<< HEAD
                $payment_id = Payment::where('reference', $request['token'])->first()->id;
                $order = Order::where('id', $payment_id)->first();
                $amount = 0;
                foreach($response['purchase_units'] as $unit){
                    foreach($unit['payments']['captures'] as $capture){
                        $amount += $capture['amount']['value'];
                    }
                }
                Payment::where('reference', $request['token'])->update([
                    'amount' => $amount,
                    'provider' => 'PayPal',
                ]);
                foreach(Cart::get($order->cart_id)->items as $item){
                    $product = Product::whereId($item->id)->first();
                    if($product->available && ($product->inventory->quantity >= $item->quantity)){
                        $value = null;
                        if($product->inventory->digital){
                            foreach($product->inventory->items as $i){
                                if(!$i->valid){
                                    $value = $i->value;
                                    DB::table('skus')->where('id', $i->id)->update([
                                        'valid' => 1,
                                    ]);
                                    return false;
                                }
                            }
=======
                Order::whereId($order->id)->update(['payed' => true]);
                foreach($order->items as $item){
                    $product = Product::whereId($item->product_id)->first();
                    Inventory::whereId($product->inventory->id)->update(['quantity' => $product->inventory->quantity-$item->quantity]);
                    if($product->inventory->digital == true){
                        foreach(Sku::where('inventory_id', $product->inventory->id)->where('valid', true)->get()->take($item->quantity) as $key){
                            Sku::whereId($key->id)->update(['valid' => false]);
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
                        }
                        DB::table('order-items')->insert([
                            'order_id' => $order->id,
                            'product_id' => $item->id,
                            'quantity' => $item->quantity,
                            'value' => $value,
                        ]);
                    }
<<<<<<< HEAD
                    Inventory::whereId($product->inventory->id)->update([
                        'quantity' => $product->inventory->quantity-$item->quantity,
                    ]);
                    Cart::removeItem($item->item_id);
=======
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
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