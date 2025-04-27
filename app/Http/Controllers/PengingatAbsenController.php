<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pengajuan;
use App\Models\JadwalKerja;
use App\Models\Presensi;
use App\Models\PengingatPesan;
use Illuminate\Support\Facades\Mail;
use App\Mail\PengingatAbsenMail;
use Carbon\Carbon;

class PengingatAbsenController extends Controller
{
    public function get()
    {
        $pesan = PengingatPesan::first();
        return response()->json($pesan);
    }


    public function index()
    {
        $today = Carbon::now()->format('Y-m-d');
        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd'); // Nama hari misalnya "Senin"
        $jadwal = JadwalKerja::where('hari', ucfirst($hariIni))->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Tidak ada jadwal kerja hari ini.'], 200);
        }

        // Ambil semua user
        $users = User::with(['presensis', 'pengajuan'])->get();

        $pengingat = [];

        foreach ($users as $user) {
            $sudahAbsen = Presensi::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->exists();

            $sudahIzin = Pengajuan::where('user_id', $user->id)
                ->whereDate('tanggal_izin', $today)
                ->exists();

            if (!$sudahAbsen && !$sudahIzin) {
                $pengingat[] = [
                    'id' => $user->id,
                    'user' => [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                    ],
                    'tanggal' => $today,
                    'pesan' => 'Anda belum melakukan absen atau pengajuan izin hari ini.',
                ];
            }
        }

        return response()->json($pengingat);
    }

    public function kirimUlang()
{
    $today = Carbon::now()->format('Y-m-d');
    $hariIni = Carbon::now()->locale('id')->isoFormat('dddd');
    $jadwal = JadwalKerja::where('hari', ucfirst($hariIni))->first();

    if (!$jadwal) return response()->json(['message' => 'Tidak ada jadwal hari ini.']);

    $pesanDefault = PengingatPesan::first()->pesan ?? 'Anda belum melakukan absen atau pengajuan izin hari ini.';

    $users = User::with(['presensis', 'pengajuan'])->get();

    foreach ($users as $user) {
        $sudahAbsen = Presensi::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->exists();

        $sudahIzin = Pengajuan::where('user_id', $user->id)
            ->whereDate('tanggal_izin', $today)
            ->exists();

        if (!$sudahAbsen && !$sudahIzin) {
            Mail::to($user->email)->send(new PengingatAbsenMail($user, $pesanDefault));
        }
    }

    return response()->json(['message' => 'Pengingat dikirim ke semua yang belum absen.']);
}

    
}
