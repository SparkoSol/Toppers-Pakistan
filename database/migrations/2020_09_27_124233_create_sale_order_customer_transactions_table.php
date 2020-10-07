<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_customer_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_order_id')->unsigned();
            $table->foreign('sale_order_id')->references('id')->on('sale_orders');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('customer_transactions');
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
        Schema::dropIfExists('sale_order_customer_transactions');
    }
}
