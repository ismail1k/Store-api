<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Yab\ShoppingCart\Checkout;
use App\Models\Product;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = Product::whereId(1)->first();
        $cart = Checkout::create();
        $cart->addItem($product, 2, $product->price-$product->discount);
        Order::create([
            'cart_id' => $cart->id(),
            'user_id' => 1,
            'payment_method' => 'paypal',
            'fullname' => 'ismail andaloussi',
            'address' => 'Morocco casablanca jdoasjdads asdas 20160',
            'phone' => '0651145445',
            'note' => 'This is an additionnal note!',
        ]);
    }
}
