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
            $table->integer('user_id')->nullable()->default(null);
            $table->string('fullname');
            $table->string('address');
            $table->string('phone');
            $table->string('note')->nullable();
            $table->integer('state')->default(1);
<<<<<<< HEAD
            $table->integer('payment_id')->nullable();
=======
            $table->boolean('payed')->default(false);
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
        Schema::dropIfExists('orders');
    }
}
