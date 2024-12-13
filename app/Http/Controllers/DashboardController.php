<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;

class DashboardController extends Controller
{

    public function showDashboardPage() {
        
        return view('dashboard.dashboard');
    }
    public function index()
    {
        $jumlahUser = User::count();

        $sudahAbsen = Absensi::distinct('user_id')->count();

        $belumAbsen = $jumlahUser - $sudahAbsen;

        return response()->json([

            'jumlah_user' => $jumlahUser,
            'sudah_absen' => $sudahAbsen,
            'belum_absen' => $belumAbsen,
        ]); 
    }


}