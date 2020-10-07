<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_in_customer_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_in_id')->unsigned();
            $table->foreign('payment_in_id')->references('id')->on('payment_ins');
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
        Schema::dropIfExists('payment_in_customer_transactions');
    }
}
