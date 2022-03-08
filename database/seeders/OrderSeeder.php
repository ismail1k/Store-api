<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        Order::create([
            'product_id' => 1,
            'user_id' => 1,
            'payment_method' => 'paypal',
            'quantity' => 1,
            'fullname' => 'ismail andaloussi',
            'address' => 'Morocco casablanca jdoasjdads asdas 20160',
            'phone' => '0651145445',
            'note' => 'This is an additionnal note!',
            'state' => 1,
        ]);
    }
}
