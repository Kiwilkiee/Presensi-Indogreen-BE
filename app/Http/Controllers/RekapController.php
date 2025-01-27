<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi; // Pastikan Anda memiliki model Absensi

class RekapController extends Controller
{
    public function rekapitDate(Request $request)
    {
        // Validasi input tanggal
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->tanggal;

        $absensi = Absensi::whereDate('created_at', $tanggal)->get();

        return response()->json([
            'status' => 'success',
            'data' => $absensi,
        ]);
    }
}
