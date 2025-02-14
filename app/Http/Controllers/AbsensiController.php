<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalKerja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AbsensiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(){

        $absensis = Absensi::all();
        
        return response()->json($absensis);
    }

    public function getAbsensiById($userId) 
    {
        $absensis = Absensi::where('user_id', $userId)
        ->orderBY('jam_masuk', 'desc')
        ->get();

        return response()->json($absensis);
    }

    public function absenMasuk(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        Log::info('Request Data:', $request->all());

        // Cek apakah user sudah absen masuk hari ini
        $existingAbsensi = Absensi::where('user_id', $request->user_id)
            ->whereDate('jam_masuk', Carbon::today())
            ->first();

        if ($existingAbsensi) {
            Log::warning('User sudah absen masuk hari ini:', ['user_id' => $request->user_id]);
            return response()->json(['message' => 'User sudah absen masuk hari ini'], 400);
        }

        // Jika belum absen, buat absen baru
        $absensi = Absensi::create([
            'user_id' => $request->user_id,
            'jam_masuk' => Carbon::now(),
        ]);

        return response()->json($absensi);
    }

    public function absenPulang(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        Log::info('Request Data:', $request->all());

        // Cek apakah user sudah absen masuk dan belum absen pulang hari ini
        $absensi = Absensi::where('user_id', $request->user_id)
            ->whereDate('jam_masuk', Carbon::today())
            ->whereNull('jam_pulang')
            ->first();

        if (!$absensi) {
            Log::warning('User belum absen masuk atau sudah absen pulang hari ini:', ['user_id' => $request->user_id]);
            return response()->json(['message' => 'User belum absen masuk atau sudah absen pulang hari ini'], 400);
        }

        // Update jam pulang
        $absensi->jam_pulang = Carbon::now();
        $absensi->save();

        return response()->json($absensi);
    }

    public function getAbsensiHariIni()
    {
        $today = Carbon::now()->toDateString();
        
        $karyawan = DB::table('users')
            ->leftJoin('absensi', function ($join) use ($today) {
                $join->on('users.id', '=', 'absensi.user_id')
                    ->whereDate('absensi.jam_masuk', $today);
            })
            ->leftJoin('jadwal_kerja', function ($join) use ($today) {
                $join->on(DB::raw('DATE(jadwal_kerja.tanggal)'), '=', DB::raw("'$today'"));
            })
            ->select(
                'users.id',
                'users.name',
                'absensi.jam_masuk',
                'absensi.jam_pulang',
                DB::raw("CASE 
                            WHEN jadwal_kerja.is_libur = 1 THEN 'Libur'
                            WHEN absensi.jam_masuk IS NOT NULL THEN 'Hadir'
                            ELSE 'Tidak Hadir'
                        END as status")
            )
            ->get();

        return response()->json($karyawan);
    }

    public function getRekapByDate(Request $request)
    {
        // Ambil tanggal dari request atau gunakan tanggal hari ini sebagai default
        $date = $request->query('date', Carbon::today()->toDateString());

        Log::info('Tanggal yang diterima: ' . $date);

        // Gunakan DATE() untuk menyesuaikan format dengan database
        $absensi = Absensi::whereRaw("DATE(jam_masuk) = ?", [$date])
                    ->with('user:id,name')

                     // Ambil data user (nama)
                    ->orderBy('jam_masuk', 'asc')
                    ->get();

        Log::info('Data absensi:', $absensi->toArray());

        // Jika tidak ada data, kembalikan pesan kosong
        if ($absensi->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data absensi pada tanggal ini.',
                'data' => []
            ], 200);
        }

        return response()->json($absensi);
    }
}
