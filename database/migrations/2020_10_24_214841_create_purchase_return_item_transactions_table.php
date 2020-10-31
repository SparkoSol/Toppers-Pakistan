<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnItemTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_item_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_return_id')->unsigned();
            $table->foreign('purchase_return_id')->references('id')->on('purchase_returns');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('item_transactions');
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
        Schema::dropIfExists('purchase_return_item_transactions');
    }
}
