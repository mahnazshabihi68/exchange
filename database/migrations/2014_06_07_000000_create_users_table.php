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

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('national_code')->nullable();
            $table->string('birthday')->nullable();
            $table->string('email')->nullable();
            $table->boolean('email_is_verified')->default(false);
            $table->string('mobile')->nullable();
            $table->boolean('mobile_is_verified')->default(false);
            $table->string('password')->nullable();
            $table->string('avatar')->default('avatar.png')->nullable();
            $table->boolean('two_factor_is_enabled')->default(false);
            $table->string('two_factor_type')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->timestamp('two_factor_is_verified_until')->nullable();
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->foreign('referrer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ethereum_address')->nullable();
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
        Schema::dropIfExists('users');
    }
}
