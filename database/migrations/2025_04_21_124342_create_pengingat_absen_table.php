<?php

// database/migrations/xxxx_xx_xx_create_pengingat_absen_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengingatAbsenTable extends Migration
{
    public function up()
    {
        Schema::create('pengingat_absen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->text('pesan');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('pengingat_absen');
    }
};

