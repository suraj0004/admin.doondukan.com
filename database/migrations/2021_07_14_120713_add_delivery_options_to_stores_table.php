<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryOptionsToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('delivery_medium', ['user-self-collected', 'shop-delivery','delivery-partner'])->after('logo')->nullable();
            $table->unsignedInteger('order_within_km')->after('delivery_medium')->nullable();
            $table->unsignedInteger('minimum_order_amount')->after('order_within_km')->nullable();
            $table->unsignedInteger('delivery_charges')->after('minimum_order_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('delivery_medium');
            $table->dropColumn('order_within_km');
            $table->dropColumn('minimum_order_amount');
            $table->dropColumn('delivery_charges');
        });
    }
}
