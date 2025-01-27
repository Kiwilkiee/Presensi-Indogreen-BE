<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
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

    // public function getRekapWithUserDetails(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'date' => 'required|date',
    //     ]);

    //     $date = $validatedData['date'];

    //     $absensis = Absensi::select(
    //             'absensis.id', 
    //             'users.nama as nama_user', 
    //             DB::raw('DATE(absensis.jam_masuk) as tanggal'), 
    //             'absensis.jam_masuk', 
    //             'absensis.jam_pulang'
    //         )
    //         ->join('users', 'users.id', '=', 'absensis.user_id')
    //         ->whereDate('absensis.jam_masuk', $date)
    //         ->orWhereDate('absensis.jam_pulang', $date)
    //         ->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $absensis
    //     ]);
    // }

    

}
