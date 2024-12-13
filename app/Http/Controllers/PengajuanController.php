<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;


class PengajuanController extends Controller
{
   public function store(Request $request) 
   {
        $request->validate([
            'user_id' => 'required|integer',
            'tanggal_izin' => 'required|date',
            'status' => 'required|string',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:50240',
        ]); 

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
        }

        Pengajuan::create([
            'user_id' => $request->user_id,
            'tanggal_izin' => $request->tanggal_izin,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath,
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil ditambahkan',
        ], 201);
   }
}
