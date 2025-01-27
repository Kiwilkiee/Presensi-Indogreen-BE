<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        
        $guardName = 'api';  

        // Permissions
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'absen masuk']);
        Permission::create(['name' => 'absen pulang']);
        Permission::create(['name' => 'edit profil']);

        // Roles
        $adminRole = Role::create(['name' => 'admin']);
        $karyawanRole = Role::create(['name' => 'karyawan']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(['view dashboard', 'manage users', 'absen masuk', 'absen pulang', 'edit profil']);
        $karyawanRole->givePermissionTo(['absen masuk', 'absen pulang', 'edit profil']);
    }
}