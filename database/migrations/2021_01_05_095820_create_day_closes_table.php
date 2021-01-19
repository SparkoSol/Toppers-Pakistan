<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayClosesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_closes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cash');
            $table->string('expense');
            $table->integer('purchase');
            $table->string('sale');
            $table->string('stock');
            $table->string('stockValue');
            $table->string('toPay');
            $table->string('toReceive');
            $table->string('date');
            $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('restaurant_branches');
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
        Schema::dropIfExists('day_closes');
    }
}
