<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;


class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = Pengajuan::with('user')->get();
        return response()->json($pengajuan);
    }

    public function store(Request $request) 
    {

        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    

        $request->validate([
            'tanggal_izin' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_izin',
            'keterangan' => 'required|string',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:50240',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
        }

        Pengajuan::create([
            'user_id' =>  $user->id,
            'tanggal_izin' => $request->tanggal_izin,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath,
            'status' => 'Menunggu',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil ditambahkan',
        ], 201);
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);
        return response()->json($pengajuan);
    }

    public function getById($userId)
    {
        $pengajuan = Pengajuan::with('user')
            ->where('user_id', $userId)
            ->get();

        return response()->json($pengajuan);
    }


    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Menunggu,Diterima,Ditolak',
        'alasan' => 'nullable|string', // tambahkan validasi alasan (optional)
    ]);

    $pengajuan = Pengajuan::findOrFail($id);
    $pengajuan->status = $request->status;

    // Simpan alasan jika status Ditolak
    if ($request->status === 'Ditolak') {
        $pengajuan->alasan = $request->alasan;
    } else {
        $pengajuan->alasan = null; // reset alasan kalau bukan Ditolak
    }

    $pengajuan->save();

    // Jika status diterima, buatkan entry di tabel presensi
    if ($request->status === 'Diterima') {
        $start = \Carbon\Carbon::parse($pengajuan->tanggal_izin);
        $end = \Carbon\Carbon::parse($pengajuan->tanggal_selesai ?? $pengajuan->tanggal_izin);

        while ($start->lte($end)) {
            Presensi::updateOrCreate(
                [
                    'user_id' => $pengajuan->user_id,
                    'tanggal' => $start->toDateString(),
                ],
                [   
                    'status' => $pengajuan->keterangan,
                    'status_kehadiran' => null,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'foto_masuk' => null,
                    'foto_pulang' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'menit_terlambat' => null,
                ]
            );
            $start->addDay();
        }
    }


    return response()->json([
        'message' => 'Status pengajuan diperbarui menjadi ' . $request->status,
    ]);
}

}
