<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('logs', static function (Blueprint $table) {
            //Add
            $table->string('channel')->after('loggable_id')->nullable();
            $table->string('level')->after('channel')->nullable();
            $table->longText('data')->after('level')->nullable();

            //Alter
            $table->integer('event')->nullable()->change();
            $table->string('ip')->nullable()->change();

            //Remove
            $table->dropColumn('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropColumns('logs', ['channel', 'level', 'data']);

        Schema::table('logs', static function (Blueprint $table) {
            //Alter
            $table->integer('event')->nullable(false)->change();
            $table->string('ip')->nullable(false)->change();

            //Add
            $table->timestamp('updated_at');
        });
    }
};
