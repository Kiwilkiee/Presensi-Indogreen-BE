<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;

class RekapController extends Controller
{
    public function Rekapitulasi(Request $request)
{
    // Validasi input bulan dan tahun
    $request->validate([
        'bulan' => 'required|integer|min:1|max:12',
        'tahun' => 'required|integer|min:2000',
    ]);

    $bulan = $request->bulan;
    $tahun = $request->tahun;

    // Ambil semua presensi berdasarkan bulan dan tahun
    $absensi = Presensi::whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->get();

    // Hitung jumlah berdasarkan status & status_kehadiran
    $rekap = [
        'hadir' => $absensi->where('status', 'Hadir')->count(),
        'izin' => $absensi->where('status', 'Izin')->count(),
        'sakit' => $absensi->where('status', 'Sakit')->count(),
        'alpha' => $absensi->where('status', 'Alpha')->count(),
        'telat' => $absensi->where('status_kehadiran', 'Terlambat')->count(),
    ];

    return response()->json([
        'status' => 'success',
        'bulan' => $bulan,
        'tahun' => $tahun,
        'rekap' => $rekap,
        'data' => $absensi,
    ]);
}

}
