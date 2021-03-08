<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('invoice_date');
            $table->string('invoice_id');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('address_id')->unsigned()->nullable();
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->integer('branch_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('restaurant_branches');
            $table->string('payment_type');
            $table->string('billing_address')->nullable();
            $table->string('amount');
            $table->string('delivery_status')->default('Pending');
            $table->string('origin')->default('Web Order');
            $table->boolean('delivery')->default(false);
            $table->string('discount')->nullable();
            $table->string('extra')->nullable();
            $table->string('instructions')->nullable()->default("No Instructions");
            $table->integer('delivery_fee');
            $table->string('balance_due')->nullable();
            $table->string('return_status')->nullable();
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
        Schema::dropIfExists('sale_orders');
    }
}
