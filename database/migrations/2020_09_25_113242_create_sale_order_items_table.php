<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_order_id')->unsigned();
            $table->foreign('sale_order_id')->references('id')->on('sale_orders');
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('variant_id')->unsigned()->nullable();
            $table->foreign('variant_id')->references('id')->on('variants');
            $table->string('price');
            $table->string('qty');
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
        Schema::dropIfExists('sale_order_items');
    }
}
