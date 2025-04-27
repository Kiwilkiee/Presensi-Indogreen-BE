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
    Schema::table('pengajuan', function (Blueprint $table) {
        $table->date('tanggal_selesai')->nullable()->after('tanggal_izin');
    });
}

public function down()
{
    Schema::table('pengajuan', function (Blueprint $table) {
        $table->dropColumn('tanggal_selesai');
    });
}

};
