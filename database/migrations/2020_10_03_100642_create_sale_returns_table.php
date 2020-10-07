<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_id');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('restaurant_branches');
            $table->integer('sale_order_id')->unsigned();
            $table->foreign('sale_order_id')->references('id')->on('sale_orders');
            $table->string('invoice_date');
            $table->string('payment_type');
            $table->string('total');
            $table->string('paid')->nullable();
            $table->string('balance')->nullable();
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
        Schema::dropIfExists('sale_returns');
    }
}
