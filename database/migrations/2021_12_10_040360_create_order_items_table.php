<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
<<<<<<< HEAD
        Schema::create('order-items', function (Blueprint $table) {
=======
        Schema::create('order_items', function (Blueprint $table) {
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
            $table->id();
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('quantity');
<<<<<<< HEAD
            $table->string('value')->nullable();
            $table->boolean('refund')->default(false);
            $table->boolean('payed')->default(false);
=======
            $table->integer('value')->nullable();
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
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
<<<<<<< HEAD
        Schema::dropIfExists('order-items');
=======
        Schema::dropIfExists('order_items');
>>>>>>> 2cbb7384b421f66802842eb592e720b94e09813d
    }
}
