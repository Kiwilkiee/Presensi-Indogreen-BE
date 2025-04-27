<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_kerjas', function (Blueprint $table) {
            $table->id();
            $table->string('hari'); // Senin, Selasa, dll.
            $table->time('jam_masuk'); // Jam masuk yang ditetapkan
            $table->timestamps();
        });

        Schema::table('presensis', function (Blueprint $table) {
            $table->string('status_kehadiran')->nullable()->after('status'); // Tepat Waktu / Terlambat
            $table->integer('menit_terlambat')->nullable()->after('status_kehadiran'); // Menyimpan jumlah menit keterlambatan
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kerjas');

        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn('status_kehadiran');
            $table->dropColumn('menit_terlambat');
        });

        
    }
};
