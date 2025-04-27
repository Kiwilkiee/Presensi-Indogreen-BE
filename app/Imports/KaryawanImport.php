<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\Permission\Models\Role;

class KaryawanImport implements ToModel
{
    public function model(array $row)
    {
        // Lewati baris header (jika ada)
        if ($row[0] === 'nama') {
            return null;
        }

        // Buat user baru
        $user = new User([
            'nama' => $row[0],
            'email' => $row[1],
            'password' => Hash::make($row[2]),
            'divisi' => $row[4],
        ]);

        $user->save(); // Wajib simpan dulu sebelum assignRole

        // Assign role dari kolom ke-4
        $roleName = strtolower(trim($row[3])); // contoh: "admin" atau "karyawan"
        if (Role::where('name', $roleName)->exists()) {
            $user->assignRole($roleName);
        }

        return $user;
    }
}
