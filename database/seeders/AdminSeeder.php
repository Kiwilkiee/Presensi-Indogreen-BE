<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::create([
            'nama' => 'admin',
            'email' => 'admin@gmail.com',
            'divisi' => 'admin',
            'password' => Hash::make('admin123'),
            
        ]);

        $user->assignRole('admin');
    }
}
