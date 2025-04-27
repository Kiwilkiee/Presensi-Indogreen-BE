<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view dashboard',
            'manage users',
            'absen masuk',
            'absen pulang',
            'edit profil'
        ];

        // Guard name
        $guard = 'api';

        // Buat permission untuk guard api
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard
            ]);
        }

        // Buat role admin & karyawan untuk guard api
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => $guard
        ]);

        $karyawanRole = Role::firstOrCreate([
            'name' => 'karyawan',
            'guard_name' => $guard
        ]);

        // Assign permission ke role
        $adminRole->syncPermissions($permissions); // Admin dapat semua
        $karyawanRole->syncPermissions([
            'absen masuk',
            'absen pulang',
            'edit profil'
        ]);
    }
}
