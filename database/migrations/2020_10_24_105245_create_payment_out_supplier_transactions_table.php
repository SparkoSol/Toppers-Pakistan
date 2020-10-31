<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentOutSupplierTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_out_supplier_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_out_id')->unsigned();
            $table->foreign('payment_out_id')->references('id')->on('payment_outs');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('supplier_transactions');
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
        Schema::dropIfExists('payment_out_supplier_transactions');
    }
}
