<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index() {
        $presensi = Presensi::all();

        return response()->json($presensi, 200);
    }

    public function PresensiMasuk(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'foto_masuk' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi_masuk' => 'required|string'
        ]);

        // Cek apakah sudah absen hari ini
        $cekPresensi = Presensi::where('user_id', $request->user_id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if ($cekPresensi) {
            return response()->json([ 'message' => 'Sudah absen masuk hari ini' ], 400);
        }

        // Simpan foto masuk jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_masuk')) {
            $fotoPath = $request->file('foto_masuk')->store('foto_masuk', 'public');
        }

        // Buat presensi baru dengan informasi tanggal, jam, dan lokasi saat itu
        $presensi = Presensi::create([
            'user_id' => $request->user_id,
            'tanggal' => now()->toDateString(), 
            'jam_masuk' => now()->toTimeString(), 
            'foto_masuk' => $fotoPath, 
            'lokasi_masuk' => $request->lokasi_masuk,
        ]);

        return response()->json([
            'message' => 'Berhasil absen masuk',
            'data' => [
                $presensi
                            ]
        ], 200);
    }

    public function PresensiPulang(Request $request) {

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'foto_pulang' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi_pulang' => 'required|string'
        ]);

        $presensi = Presensi::where('user_id', $request->user_id)
            ->whereDate('jam_masuk', Carbon::today())
            ->whereNull('jam_pulang')
            ->first();

        if (!$presensi) {
            return response()->json([ 'message' => 'Use Sudah Melakukan Absen Pulang Atau Belm Melakukan Absensi Masuk'], 400);
        }

        // Simpan foto pulang jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_pulang')) {
            $fotoPath = $request->file('foto_pulang')->store('foto_pulang', 'public');
        }

        // Update presensi dengan informasi jam pulang dan lokasi saat itu
        $presensi->update([
            'jam_pulang'    => Carbon::now()->toTimeString(),
            'foto_pulang'   => $fotoPath,
            'lokasi_pulang' => $request->lokasi_pulang,
        ]);

        return response()->json([
            'message' => 'Berhasil absen pulang',
            'data' => [
                $presensi
                            ]
        ], 200);
    }

    public function PresensiHariIni($userId)
    {
        $presensi = Presensi::where('user_id', $userId)
            ->orderBy('jam_masuk', 'desc')
            ->get();


            if ($presensi->isEmpty()) {
                return response()->json([ 'message' => 'User belum melakukan absen hari ini'], 400);
            } else {
                return response()->json($presensi, 200);
            }
    }

    public function RekapPresensiByDate(Request $request) 
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $validatedData['tanggal'];

        $presensi = Presensi::with('user')
            ->whereDate('jam_masuk', $tanggal)
            ->orWhereDate('jam_pulang', $tanggal)
            ->get();

            if ($presensi ->isEmpty()) {
                return response()->json([ 'message' => 'Tidak Ada Absensi Pada Tanggal Ini'], 400);
            } else {
                return response()->json($presensi);
            }

    }
}
