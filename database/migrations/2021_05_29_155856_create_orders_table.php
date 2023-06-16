<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('engine')->nullable();
            $table->string('engine_order_id')->nullable();
            $table->string('internal_order_id')->nullable();
            $table->string('market')->nullable();
            $table->string('type')->nullable();
            $table->string('side')->nullable();
            $table->float('executed_price', 32, 8)->nullable();
            $table->float('original_price', 32, 8)->nullable();
            $table->float('stop_price', 32, 8)->nullable();
            $table->float('original_market_price', 32, 8)->nullable();
            $table->float('original_quantity', 32, 8);
            $table->float('executed_quantity', 32, 8)->default(0);
            $table->float('wage_amount', 32, 8)->default(0);
            $table->float('fill_percentage', 5)->default(0);
            $table->float('cumulative_quote_quantity', 32, 8)->default(0);
            $table->string('status')->default('NEW');
            $table->boolean('is_virtual')->default(false);
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
        Schema::dropIfExists('orders');
    }
}
