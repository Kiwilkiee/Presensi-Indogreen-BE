<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengingatPesan;

class PesanPengingatController extends Controller
{
    public function get()
    {
        $pesan = PengingatPesan::first();
        return response()->json($pesan);
    }

    public function update(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string'
        ]);

        $pesan = PengingatPesan::firstOrCreate([], ['pesan' => $request->pesan]);
        $pesan->pesan = $request->pesan;
        $pesan->save();


        return response()->json(['message' => 'Pesan berhasil disimpan.']);
    }
}