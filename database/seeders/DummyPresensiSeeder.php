<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\User;
use App\Models\JadwalKerja;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DummyPresensiSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::parse('2024-12-01');
        $endDate = Carbon::today();
        $users = User::all();

        $jadwal = JadwalKerja::whereRaw('LOWER(hari) = ?', ['senin'])->first(); // Asumsi jadwal sama tiap hari

        // Simpan foto dummy kalau belum ada
        $fotoMasukPath = 'foto_masuk/dummy_masuk.jpeg';
        $fotoPulangPath = 'foto_pulang/dummy_pulang.jpeg';
        $gambarPengajuanPath = 'gambar/dummy_pengajuan.jpeg';

        if (!Storage::disk('public')->exists($fotoMasukPath)) {
            Storage::disk('public')->put($fotoMasukPath, file_get_contents(public_path('foto_masuk/dummy_masuk.jpeg')));
        }

        if (!Storage::disk('public')->exists($fotoPulangPath)) {
            Storage::disk('public')->put($fotoPulangPath, file_get_contents(public_path('foto_pulang/dummy_pulang.jpeg')));
        }

        if (!Storage::disk('public')->exists($gambarPengajuanPath)) {
            Storage::disk('public')->put($gambarPengajuanPath, file_get_contents(public_path('gambar/dummy_pengajuan.jpeg')));
        }

        foreach ($users as $user) {
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $hari = $currentDate->locale('id')->isoFormat('dddd');

                // Lewati Sabtu & Minggu
                if (in_array(strtolower($hari), ['sabtu', 'minggu'])) {
                    $currentDate->addDay();
                    continue;
                }

                // Lewati jika sudah ada data presensi
                if (Presensi::where('user_id', $user->id)->whereDate('tanggal', $currentDate)->exists()) {
                    $currentDate->addDay();
                    continue;
                }

                // RANDOM: 15% kemungkinan user mengajukan izin/sakit/cuti
                if (rand(1, 100) <= 15) {
                    $keteranganList = ['Izin', 'Sakit', 'Cuti'];
                    $keterangan = $keteranganList[array_rand($keteranganList)];
                    $tanggalSelesai = rand(0, 1) ? $currentDate->copy()->addDay() : $currentDate->copy(); // 1 atau 2 hari

                    // Simpan pengajuan
                    Pengajuan::create([
                        'user_id' => $user->id,
                        'tanggal_izin' => $currentDate->toDateString(),
                        'tanggal_selesai' => $tanggalSelesai->toDateString(),
                        'keterangan' => $keterangan,
                        'deskripsi' => 'Dummy data pengajuan ' . strtolower($keterangan),
                        'gambar' => $gambarPengajuanPath,
                        'status' => 'Diterima',
                    ]);

                    // Simpan ke presensi sesuai tanggal pengajuan
                    $loopDate = $currentDate->copy();
                    while ($loopDate->lte($tanggalSelesai)) {
                        Presensi::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'tanggal' => $loopDate->toDateString(),
                            ],
                            [
                                'status' => $keterangan,
                                'status_kehadiran' => null,
                                'jam_masuk' => null,
                                'jam_pulang' => null,
                                'foto_masuk' => null,
                                'foto_pulang' => null,
                                'latitude' => null,
                                'longitude' => null,
                                'menit_terlambat' => null,
                            ]
                        );
                        $loopDate->addDay();
                    }

                    $currentDate = $tanggalSelesai->copy()->addDay();
                    continue;
                }

                // Jam masuk random antara 06:50 - 07:30
                $jamMasukHour = 6;
                $jamMasukMinute = rand(50, 59);
                if (rand(0, 1)) {
                    $jamMasukHour = 7;
                    $jamMasukMinute = rand(0, 30);
                }
                $jamMasuk = Carbon::parse($currentDate->format('Y-m-d') . " {$jamMasukHour}:{$jamMasukMinute}");

                // Jam pulang random 16:00 - 17:30
                $jamPulang = Carbon::parse($currentDate->format('Y-m-d') . ' ' . rand(16, 17) . ':' . rand(0, 59));

                // Hitung keterlambatan
                $statusKehadiran = 'Tepat Waktu';
                $menitTerlambat = 0;

                if ($jadwal) {
                    $jadwalMasuk = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $jadwal->jam_masuk);
                    if ($jamMasuk->gt($jadwalMasuk)) {
                        $statusKehadiran = 'Terlambat';
                        $menitTerlambat = $jamMasuk->diffInMinutes($jadwalMasuk);
                    }
                }

                // Simpan presensi normal
                Presensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $currentDate->toDateString(),
                    'jam_masuk' => $jamMasuk->format('H:i:s'),
                    'jam_pulang' => $jamPulang->format('H:i:s'),
                    'latitude' => '-6.2',
                    'longitude' => '106.8',
                    'foto_masuk' => $fotoMasukPath,
                    'foto_pulang' => $fotoPulangPath,
                    'status' => 'Hadir',
                    'status_kehadiran' => $statusKehadiran,
                    'menit_terlambat' => $menitTerlambat
                ]);

                $currentDate->addDay();
            }
        }
    }
}
