<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{

    public function showDashboardPage() {
        
        return view('dashboard.dashboard');
    }
    public function index()
{
    
    $jumlahUser = User::count();
    $sudahAbsen = Absensi::whereDate('created_at', today())->count();
    $belumAbsen = $jumlahUser - $sudahAbsen;

    // Perhitungan rata-rata waktu
    $averageMasuk = Absensi::whereNotNull('jam_masuk')
        ->avg(DB::raw("TIME_TO_SEC(jam_masuk)"));
    $averagePulang = Absensi::whereNotNull('jam_pulang')
        ->avg(DB::raw("TIME_TO_SEC(jam_pulang)"));

    // Konversi kembali dari detik ke format waktu HH:MM
    $averageTime = [
        'masuk' => $averageMasuk ? gmdate('H:i', $averageMasuk) : null,
        'pulang' => $averagePulang ? gmdate('H:i', $averagePulang) : null,
    ];

    return response()->json([
        'jumlah_user' => $jumlahUser,
        'sudah_absen' => $sudahAbsen,
        'belum_absen' => $belumAbsen,
        'average_time' => $averageTime,
    ]);
}



}