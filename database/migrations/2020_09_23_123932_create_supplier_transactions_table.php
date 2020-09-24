<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_transactions', function (Blueprint $table) {
             $table->increments('id');
             $table->string('name')->nullable();
             $table->integer('supplier_id')->unsigned();
             $table->foreign('supplier_id')->references('id')->on('suppliers');
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
        Schema::dropIfExists('supplier_transactions');
    }
}
