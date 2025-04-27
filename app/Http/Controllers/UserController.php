<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Imports\KaryawanImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function showProfile()
    {
        return view('profile.profile');
    }
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:150',
            'email' => 'required|email|unique:users',
            'divisi' => 'required',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',
        ]);
    
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'divisi' => $request->divisi,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
    
       
        $user->assignRole($request->role);

        $permissions = $user->getAllPermissions()->pluck('name');
    
        return response()->json([
            'user' => $user,
            'role' => $user->getRoleNames()->first(),
            'permissions' => $permissions,
        ], 201);
    }
    

    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi field yang ada dalam request
        $request->validate([
            'nama' => 'sometimes|required|max:150',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'divisi' => 'sometimes|required',
            'password' => 'sometimes|required|min:6',
        ]);

        // Update hanya field yang ada dalam request
        if ($request->filled('nama')) {
            $user->nama = $request->nama;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('divisi')) {
            $user->divisi = $request->divisi;
        }
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    
        
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new KaryawanImport, $request->file('file'));

        return response()->json(['message' => 'Import berhasil'], 200);
    }
}
