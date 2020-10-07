<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_return_customer_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_return_id')->unsigned();
            $table->foreign('sale_return_id')->references('id')->on('sale_returns');
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
        Schema::dropIfExists('sale_return_customer_transactions');
    }
}
