<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use App\Models\Order;

class PaymentController extends Controller
{
    public function paypal(Request $request){
        $order = OrderController::get($request['order_id']);
        if(config('paypal.settings.mode') == 'live'){
            $client_id = config('paypal.live.client_id');
            $secret = config('paypal.live.secret');
        } else {
            $client_id = config('paypal.sandbox.client_id');
            $secret = config('paypal.sandbox.secret');
        }
        $apiContext = new ApiContext(new OAuthTokenCredential($client_id, $secret));
        $apiContext->setConfig(config('paypal.settings'));


        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
        $items = [];
        $total = 0;
        foreach($order['cart']['items'] as $cart){
            $total += $cart['price'];
            $item = new Item();
            $item->setName($cart['name'])
                ->setCurrency('USD')
                ->setDescription($cart['name'])
                ->setQuantity($cart['quantity'])
                ->setPrice($cart['unit_price']);
            array_push($items, $item);
        }
        $itemList = new ItemList();
        $itemList->setItems($items);
        $details = new Details();
        $details->setShipping($total)
            ->setTax(0)
            ->setSubtotal($total);
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($total)
            ->setDetails($details);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList);
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl(url('payment.paypal.execute').'?success=true')
            ->setCancelUrl(url('payment.paypal.execute').'?success=false');

        $payment = new Payment();
        $payment->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        try{
            $response = $payment->create($apiContext);
            return response()->json($response);
        } catch(Exception $e){
            return response()->json($e);
        }
        return response()->json([
            'status' => 200,
            'order' => $payment->getApprovalLink(),
        ]);

        
        // $order = OrderController::get($request['order_id']);
        // $createOrder = [
        //     "intent" => "CAPTURE",
        //     "purchase_units" => [],
        // ];
        // foreach($order['cart']['items'] as $item){
        //     array_push($createOrder['purchase_units'], [
        //         "name" => $item['name'],
        //         // "description" => $item['description'],
        //         "type" => $item['type'],
        //         "amount" => [
        //             "currency_code" => 'USD',
        //             "quantity" => $item['quantity'],
        //             "value" => $item['unit_price'],
        //         ],
        //     ]);
        // }
        // $provider = new PayPalClient;
        // $response = $provider->createOrder($createOrder);
    }
}
