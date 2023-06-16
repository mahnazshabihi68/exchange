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

class CreateSymbolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('symbols', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('name_fa')->nullable();
            $table->string('name_en')->nullable();
            $table->string('picture')->nullable();
            $table->boolean('is_withdrawable')->default(true);
            $table->boolean('is_depositable')->default(true);
            $table->float('min_withdrawable_quantity', 32, 8)->nullable();
            $table->float('max_withdrawable_quantity', 32, 8)->nullable();
            $table->integer('precision')->default(6);
            $table->timestamps();
        });

        Schema::create('blockchain_symbol', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blockchain_id');
            $table->foreign('blockchain_id')->references('id')->on('blockchains')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('symbol_id');
            $table->foreign('symbol_id')->references('id')->on('symbols')->onUpdate('cascade')->onDelete('cascade');
            $table->float('transfer_fee', 32, 8)->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('symbols');

        Schema::dropIfExists('blockchain_symbol');
    }
}
