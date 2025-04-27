<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('presensis', function (Blueprint $table) {
        $table->time('jam_masuk')->nullable()->change();
        $table->string('foto_masuk')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->time('jam_masuk')->nullable(false)->change();
            $table->string('foto_masuk')->nullable(false)->change();
        });
    }
};
