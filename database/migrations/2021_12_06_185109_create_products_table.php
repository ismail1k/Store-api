<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('short_description');
            $table->longText('description');
            $table->string('tags');
            $table->integer('media_id');
            $table->integer('category_id');
            $table->integer('inventory_id');
            $table->float('price');
            $table->float('discount')->default(0.00);
            $table->boolean('virtual');
            $table->boolean('available')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('products');
    }
}
