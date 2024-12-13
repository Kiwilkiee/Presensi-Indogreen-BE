<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Carbon\Carbon;
use App\Http\Resources\AbsensiResource;


class AbsensiTodayController extends Controller
{
    public function getAbsensiToday() 
    {
        // Mendapatkan tanggal hari ini dalam format tanggal saja (tanpa waktu)
        $today = Carbon::now()->toDateString();
        
        // Mengambil data absensi dari tabel absensi yang terjadi hari ini
        $absensiToday = Absensi::with('user')
            ->whereDate('created_at', $today)
            ->get();
        
        // Cek apakah data absensi kosong
        if ($absensiToday->isEmpty()) {
            // Jika tidak ada yang absen hari ini, kembalikan respon error
            return response()->json([
                'message' => 'Belum ada yang absen hari ini.'
            ], 404);
        }

        // Mengembalikan data dalam format JSON jika ada yang absen hari ini
        return AbsensiResource::collection($absensiToday);
    }
}
