<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presensi;
use Carbon\Carbon;

class UpdateAbsenAlpha extends Command
{
    protected $signature = 'absensi:update-alpha';
    protected $description = 'Update status Alpha untuk yang tidak absen masuk';

    public function handle()
    {
        $hariKemarin = Carbon::yesterday();

        Presensi::whereDate('tanggal', $hariKemarin)
            ->whereNull('jam_masuk')
            ->update(['status' => 'Alpha']);

        $this->info('Status Alpha berhasil diperbarui.');
    }
}