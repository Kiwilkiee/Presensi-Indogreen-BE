<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKerja;

class JadwalKerjaController extends Controller
{
    // Tampilkan semua jadwal kerja
    public function index()
    {
        $jadwal = JadwalKerja::all();
        return response()->json($jadwal);
    }

    // Tambah atau update jadwal kerja untuk hari tertentu
    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'jam_masuk' => 'required|date_format:H:i',
        ]);

        $jadwal = JadwalKerja::updateOrCreate(
            ['hari' => ucfirst(strtolower($request->hari))],
            ['jam_masuk' => $request->jam_masuk]
        );

        return response()->json([
            'message' => 'Jadwal kerja berhasil disimpan.',
            'data' => $jadwal
        ]);
    }

    // Menampilkan jadwal kerja berdasarkan hari
    public function show($hari)
    {
        $jadwal = JadwalKerja::where('hari', ucfirst(strtolower($hari)))->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Jadwal tidak ditemukan.'], 404);
        }

        return response()->json($jadwal);
    }

    // Hapus jadwal kerja berdasarkan hari
    public function destroy($hari)
    {
        $jadwal = JadwalKerja::where('hari', ucfirst(strtolower($hari)))->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Jadwal tidak ditemukan.'], 404);
        }

        $jadwal->delete();

        return response()->json(['message' => 'Jadwal kerja berhasil dihapus.']);
    }
}
