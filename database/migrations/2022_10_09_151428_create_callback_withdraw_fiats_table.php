<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callback_withdraw_fiats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('withdraw_id')->nullable();
            $table->foreign('withdraw_id')->references('id')->on('withdraws')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount', 32, 8);
            $table->string('currency');
            $table->string('description');
            $table->string('factor_number');
            $table->string('destination_iban_number');
            $table->string('owner_name');
            $table->string('reference_id');
            $table->string('source_iban_number');
            $table->string('transaction_status');
            $table->string('transfer_description');
            $table->string('transfer_status');
            $table->string('tracker_id');
            $table->string('transaction_id')->nullable();
            $table->boolean('cancelable')->default(false);
            $table->boolean('suspendable')->default(false);
            $table->boolean('changeable')->default(false);
            $table->boolean('resumeable')->default(false);
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
        Schema::dropIfExists('callback_withdraw_fiats');
    }
};
