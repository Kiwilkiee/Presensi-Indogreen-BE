<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Setting;
use App\Models\JadwalKerja;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AbsensiResource;
use Illuminate\Support\Facades\Log;


class PresensiController extends Controller
{

    public function index(Request $request)
{
    $query = Presensi::query();

    if ($request->filled('bulan') && $request->filled('tahun')) {
        // pakai kolom tanggal (YYYY-MM-DD)
        $query->whereMonth('tanggal', $request->bulan)
              ->whereYear ('tanggal', $request->tahun);
              
        // —– atau kalau kamu ingin pakai jam_masuk
        // $query->whereMonth('jam_masuk', $request->bulan)
        //       ->whereYear ('jam_masuk', $request->tahun);
    }

    return response()->json($query->get());
}



    public function absenMasuk(Request $request)
{
    $user = auth('api')->user(); // pakai guard 'api'

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $hariIni = $request->is_dummy ? Carbon::parse($request->tanggal)->toDateString() : Carbon::now()->toDateString();

    // 🔒 Cek apakah user punya pengajuan izin yang diterima untuk hari ini
    $pengajuan = Pengajuan::where('user_id', $user->id)
        ->where('tanggal_izin', $hariIni)
        ->where('status', 'Diterima')
        ->first();

    if ($pengajuan) {
        return response()->json([
            'message' => 'Anda tidak bisa absen karena sudah mengajukan izin ' . $pengajuan->keterangan . ' dan telah diterima.'
        ], 403);
    }

    // Cek apakah sudah absen hari ini
    $sudahAbsen = Presensi::where('user_id', $user->id)
        ->where('tanggal', $hariIni)
        ->first();

    if ($sudahAbsen && $sudahAbsen->jam_masuk) {
        return response()->json([
            'message' => 'Anda sudah melakukan absen masuk hari ini.'
        ], 400);
    }

    $request->validate([
        'latitude' => 'required',
        'longitude' => 'required',
        'foto_masuk' => 'required|image|max:2048'
    ]);

    $foto = $request->file('foto_masuk');
    $path = $foto->store('foto_masuk', 'public');

    $hari = Carbon::parse($hariIni)->locale('id')->isoFormat('dddd');
    Log::info('Hari ini: ' . $hari);

    $jadwal = JadwalKerja::whereRaw('LOWER(hari) = ?', [strtolower($hari)])->first();
    Log::info('Jadwal kerja ditemukan:', ['jadwal' => $jadwal]);

    $statusKehadiran = 'Tidak Ada Jadwal';
    $menitTerlambat = null;

    if ($jadwal) {
        $jamMasukJadwal = Carbon::createFromFormat('H:i:s', $jadwal->jam_masuk);
        $jamMasukSekarang = $request->is_dummy && $request->jam_masuk
            ? Carbon::parse($request->jam_masuk)
            : Carbon::now();

        if ($jamMasukSekarang->gt($jamMasukJadwal)) {
            $statusKehadiran = 'Terlambat';
            $menitTerlambat = $jamMasukSekarang->diffInMinutes($jamMasukJadwal);
        } else {
            $statusKehadiran = 'Tepat Waktu';
            $menitTerlambat = 0;
        }
    }

    $presensi = Presensi::create([
        'user_id' => $user->id,
        'tanggal' => $hariIni,
        'jam_masuk' => $request->is_dummy && $request->jam_masuk
            ? Carbon::parse($request->jam_masuk)->format('H:i:s')
            : now()->format('H:i:s'),
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'foto_masuk' => $path,
        'status' => 'Proses',
        'status_kehadiran' => $statusKehadiran,
        'menit_terlambat' => $menitTerlambat
    ]);

    Log::info('Presensi masuk berhasil', $presensi->toArray());

    return response()->json([
        'message' => 'Absen masuk berhasil.',
        'status_kehadiran' => $statusKehadiran,
        'terlambat_menit' => $menitTerlambat
    ]);
}





public function absenPulang(Request $request)
{
    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'foto_pulang' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $user = auth('api')->user(); 
    $hariIni = $request->is_dummy ? Carbon::parse($request->tanggal)->startOfDay() : Carbon::today();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }      

    $presensi = Presensi::where('user_id', $user->id)->whereDate('tanggal', $hariIni)->first();
    if (!$presensi) {
        return response()->json(['message' => 'Anda belum absen masuk!'], 400);
    }

    if ($presensi->jam_pulang) {
        return response()->json(['message' => 'Anda sudah absen pulang hari ini!'], 400);
    }

    $setting = Setting::first();
    if (!$setting) {
        return response()->json(['message' => 'Lokasi kantor belum diatur oleh admin!'], 400);
    }

    $kantorLatitude = $setting->latitude;
    $kantorLongitude = $setting->longitude;
    $radiusMaks = $setting->radius;

    $jarak = $this->hitungJarak($request->latitude, $request->longitude, $kantorLatitude, $kantorLongitude);

    if ($jarak > $radiusMaks) {
        return response()->json([
            'message' => "Anda berada di luar radius kantor! (Jarak: " . number_format($jarak, 2) . " meter)",
            'jarak' => $jarak
        ], 400);
    }

    $foto = $request->file('foto_pulang');
    $path = $foto->store('foto_pulang', 'public');

    $presensi->update([
        'jam_pulang' => $request->is_dummy && $request->jam_pulang
            ? Carbon::parse($request->jam_pulang)->format('H:i:s')
            : now(),
        'foto_pulang' => $path,
        'status' => 'Hadir',
        'message' => 'Berhasil absen pulang!', 
        'jarak' => $jarak
    ]);

    return response()->json(['message' => 'Absen pulang berhasil.']);
}


    public function updateAlpha()
    {
        $hariIni = Carbon::yesterday();

        Presensi::whereDate('tanggal', $hariIni)
            ->whereNull('jam_masuk')
            ->update(['status' => 'Alpha']);

        return response()->json(['message' => 'Status Alpha diperbarui.']);
    }

    // FUNGSI HITUNG JARAK LOKASI
    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $radiusBumi = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radiusBumi * $c;
    }
    

    public function getPresensiBulanan(Request $request)
    {
        $userId = $request->user_id ?? Auth::id();
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $presensi = Presensi::where('user_id', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        return response()->json([
            'user_id' => $userId,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'data' => $presensi
        ]);
    }

    public function getRekapTahunan(Request $request)
    {
        $userId = $request->user_id ?? Auth::id();
        $tahun = $request->tahun ?? Carbon::now()->year;

        $presensi = Presensi::where('user_id', $userId)
            ->whereYear('tanggal', $tahun)
            ->get();

        $hadir = $presensi->whereNotNull('jam_masuk')->count();

        // Hitung hari kerja dalam setahun
        $hariKerja = 0;
        $mulaiTahun = Carbon::createFromDate($tahun, 1, 1);
        $akhirTahun = Carbon::createFromDate($tahun, 12, 31);
        while ($mulaiTahun->lte($akhirTahun)) {
            if ($mulaiTahun->isWeekday()) {
                $hariKerja++;
            }
            $mulaiTahun->addDay();
        }

        $tidakHadir = $hariKerja - $hadir;
        $telat = $presensi->where('status_kehadiran', 'Terlambat')->count();

        // Hitung rata-rata jam masuk
        $jamMasukList = $presensi->pluck('jam_masuk')->filter()->map(function ($jam) {
            return Carbon::parse($jam);
        });

        $rataJamMasuk = null;
        if ($jamMasukList->count() > 0) {
            $totalDetik = $jamMasukList->reduce(function ($carry, $jam) {
                return $carry + ($jam->hour * 3600 + $jam->minute * 60 + $jam->second);
            }, 0);

            $rataDetik = $totalDetik / $jamMasukList->count();
            $rataJamMasuk = gmdate('H:i:s', $rataDetik);
        }

        return response()->json([
            'user_id' => $userId,
            'tahun' => $tahun,
            'hadir' => $hadir,
            'tidak_hadir' => $tidakHadir,
            'telat' => $telat,
            'rata_rata_jam_masuk' => $rataJamMasuk
        ]);
    }

    public function getAbsensiById($id) 
    {
        $presensi = Presensi::where('user_id', $id)->get();
        if ($presensi->isEmpty()) {
            return response()->json([
                'message' => 'Data presensi tidak ditemukan'
            ], 404);
        }
    
        // Return data
        return response()->json($presensi);
    }


    public function getAbsensiToday() 
    {
        $today = Carbon::now()->toDateString();
        
        $absensiToday = Presensi::with('user')
            ->whereDate('created_at', $today)
            ->get();
        
        if ($absensiToday->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada yang absen hari ini.'
            ], 404);
        } else {
            return response()->json([
                'message' => 'Data absensi hari ini',
                'data' => $absensiToday
            ]);
        }
    }
}
