<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index()
    {
        $jumlahUser = User::count();
        $sudahAbsen = Absensi::whereDate('created_at', Carbon::today())->distinct('user_id')->count();
        $belumAbsen = $jumlahUser - $sudahAbsen;

        $averageMasuk = Absensi::whereNotNull('jam_masuk')->avg('jam_masuk');
        $averagePulang = Absensi::whereNotNull('jam_pulang')->avg('jam_pulang');

        $formattedMasuk = $averageMasuk ? Carbon::parse($averageMasuk)->format('H:i') : '00:00';
        $formattedPulang = $averagePulang ? Carbon::parse($averagePulang)->format('H:i') : '00:00';

        return response()->json([
            'jumlah_user' => $jumlahUser,
            'sudah_absen' => $sudahAbsen,
            'belum_absen' => $belumAbsen,
            'average_time' => [
                'masuk' => $formattedMasuk,
                'pulang' => $formattedPulang,
            ],
        ]);
    }
}
