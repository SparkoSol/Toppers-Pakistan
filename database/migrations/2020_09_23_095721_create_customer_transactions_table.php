<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_transactions', function (Blueprint $table) {
             $table->increments('id');
             $table->string('name')->nullable();
             $table->integer('customer_id')->unsigned();
             $table->foreign('customer_id')->references('id')->on('customers');
             $table->integer('quantity');
             $table->integer('value');
             $table->integer('action_type');
             $table->date('date');
             $table->string('status')->nullable();
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
        Schema::dropIfExists('customer_transactions');
    }
}
