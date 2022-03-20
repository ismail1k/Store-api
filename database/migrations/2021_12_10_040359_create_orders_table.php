<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('cart_id');
            $table->string('fullname');
            $table->string('address');
            $table->string('phone');
            $table->string('note')->nullable();
            $table->integer('state')->default(1);
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable()->default(null);
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_transaction_id_foreign');
            $table->dropColumn('transaction_id');
        });
        Schema::dropIfExists('orders');
    }
}
