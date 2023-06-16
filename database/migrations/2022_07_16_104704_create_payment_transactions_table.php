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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_id');
            $table->foreign('deposit_id')->references('id')->on('deposits')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount', 32, 8);
            $table->string('trans_id')->nullable();
            $table->string('ref_number')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('factor_number')->nullable();
            $table->string('mobile')->nullable();
            $table->string('description')->nullable();
            $table->string('card_number')->nullable();
            $table->text('CID')->nullable();
            $table->integer('code');
            $table->boolean('status');
            $table->string('message');
            $table->timestamp('trans_created_date');
            $table->timestamp('payment_date');
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
        Schema::dropIfExists('payment_transactions');
    }
};
