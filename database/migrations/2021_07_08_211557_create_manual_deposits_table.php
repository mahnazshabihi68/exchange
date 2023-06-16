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

class CreateManualDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount', 32, 8)->default(0);
            $table->unsignedBigInteger('symbol_id')->nullable();
            $table->foreign('symbol_id')->references('id')->on('symbols')->onUpdate('cascade')->onDelete('cascade');

            $table->string('ref');
            $table->tinyInteger('status')->default(1);
            $table->string('picture')->nullable();
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
        Schema::dropIfExists('manual_deposits');
    }
}
