<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_ins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receipt_id');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('restaurant_branches');
            $table->integer('sale_order_id')->unsigned()->nullable();
            $table->foreign('sale_order_id')->references('id')->on('sale_orders');
            $table->dateTime('receipt_date');
            $table->string('description')->nullable();
            $table->string('received');
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
        Schema::dropIfExists('payment_ins');
    }
}
