<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_id');
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('restaurant_branches');
            $table->integer('purchase_order_id')->unsigned();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->dateTime('invoice_date');
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
        Schema::dropIfExists('purchase_returns');
    }
}
