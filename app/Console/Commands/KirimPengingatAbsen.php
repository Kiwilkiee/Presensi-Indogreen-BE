<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use App\Models\Pengajuan;
use App\Models\PengingatAbsen;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class KirimPengingatAbsen extends Command
{
    protected $signature = 'pengingat:absen';
    protected $description = 'Mengirim pengingat absen jika user belum absen dan tidak ada pengajuan izin';

    public function handle()
    {
        $hariIni = Carbon::now()->format('l'); // Senin, Selasa, dst.
        $tanggal = Carbon::now()->toDateString();
        $jamSekarang = Carbon::now()->format('H:i');

        $jadwalHariIni = JadwalKerja::where('hari', ucfirst(strtolower($hariIni)))->first();

        if (!$jadwalHariIni) {
            $this->info('Tidak ada jadwal kerja hari ini.');
            return;
        }

        $jamMasuk = $jadwalHariIni->jam_masuk;

        if ($jamSekarang < $jamMasuk) {
            $this->info("Masih sebelum jam masuk ($jamMasuk). Tidak kirim pengingat.");
            return;
        }

        $users = User::all();

        foreach ($users as $user) {
            $sudahAbsen = Absensi::where('user_id', $user->id)
                ->whereDate('created_at', $tanggal)
                ->exists();

            $adaPengajuan = Pengajuan::where('user_id', $user->id)
                ->whereDate('tanggal_izin', $tanggal)
                ->exists();

            if (!$sudahAbsen && !$adaPengajuan) {
                $sudahAdaPengingat = PengingatAbsen::where('user_id', $user->id)
                    ->whereDate('tanggal', $tanggal)
                    ->exists();

                if (!$sudahAdaPengingat) {
                    $pesan = "Hai {$user->name}, kamu belum melakukan absen hari ini. Jangan lupa absen ya!";

                    PengingatAbsen::create([
                        'user_id' => $user->id,
                        'tanggal' => $tanggal,
                        'pesan' => $pesan
                    ]);

                    Mail::raw($pesan, function ($message) use ($user) {
                        $message->to($user->email)->subject('Pengingat Absen Hari Ini');
                    });

                    $this->info("Pengingat dikirim ke: {$user->email}");
                }
            }
        }

        $this->info('Selesai mengirim pengingat absen.');
    }
}
