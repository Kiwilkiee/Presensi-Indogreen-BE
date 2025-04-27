<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class PengingatAbsen extends Command
{
    protected $signature = 'pengingat:absen';
    protected $description = 'Kirim email pengingat ke karyawan yang belum absen masuk atau belum ada pengajuan di hari ini';

    public function handle()
    {
        $hariIni = Carbon::today()->toDateString();
        $jamSekarang = Carbon::now()->format('H:i:s');

        // Ambil semua user yang punya jadwal kerja hari ini
        $jadwalHariIni = JadwalKerja::whereDate('tanggal', $hariIni)->get();

        foreach ($jadwalHariIni as $jadwal) {
            $user = $jadwal->user;

            $absen = Absensi::where('user_id', $user->id)
                        ->whereDate('created_at', $hariIni)
                        ->first();

            // Jika belum absen dan jam sekarang >= jam masuk yang dijadwalkan
            if (!$absen && $jamSekarang >= $jadwal->jam_masuk) {
                // Kirim email pengingat
                Mail::raw("Hai {$user->name}, jangan lupa absen hari ini!", function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Pengingat Absen');
                });

                $this->info("Pengingat absen dikirim ke {$user->email}");
            }
        }
    }
}
