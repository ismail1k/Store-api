<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->boolean('primary')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('media');
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
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign('media_product_id_foreign');
            $table->dropColumn('product_id');
        });
        Schema::dropIfExists('media');
    }
}
