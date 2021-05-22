<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buyer_id');
            $table->foreign('buyer_id')->references('id')->on('users'); //auth->user_id
            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('id')->on('users'); //body
            $table->foreignId('store_id')->constrained();                //db->store id
            $table->foreignId('product_id')->constrained();         //body
            $table->integer('quantity');                            //body
            $table->double('price', 15, 8)->default(0)->comment('Per piece price');            //db->product
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
        Schema::dropIfExists('carts');
    }
}
