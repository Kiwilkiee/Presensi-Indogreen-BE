<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\JadwalKerjaController;
use App\Http\Controllers\PengingatAbsenController;
use App\Http\Controllers\PesanPengingatController;
use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Imports\KaryawanImport;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group.
|--------------------------------------------------------------------------
*/

// ========== AUTH ==========
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'prosesLogin']);
});


Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);



// ========== USER (Tanpa Auth) ==========
Route::post('/user', [UserController::class, 'store']);
Route::get('/user/{id}', [UserController::class, 'show']);

// ========== USER (Dengan Auth) ==========
Route::middleware('auth:api')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'index');
        Route::patch('/user/{id}', 'update');
        Route::delete('/user/{id}', 'destroy');
        Route::get('/profile', 'profile');
        Route::post('/import-user', 'import');
    });     

    // ========== ABSENSI / PRESENSI ==========
    Route::controller(PresensiController::class)->group(function () {
        Route::get('/absensi', 'index');
        Route::post('/absensi/masuk', 'absenMasuk');
        Route::post('/absensi/pulang', 'absenPulang');
        Route::get('/absensi/bulanan', 'getPresensiBulanan');
        Route::get('/absensi/tahunan', 'getRekapTahunan');
        Route::get('/absensi/today', 'getAbsensiToday');
        Route::get('/absensi/{id}', 'getAbsensiById');
        Route::get('/update-alpha', 'updateAlpha');
    });

    
    // ========== REKAP ==========
    Route::post('/rekap/ByDate', [RekapController::class, 'Rekapitulasi']);

    // ========== PENGAJUAN ==========
    Route::controller(PengajuanController::class)->group(function () {
        Route::get('/pengajuan', 'index');
        Route::post('/pengajuan', 'store');
        Route::get('/pengajuan/{id}', 'show');
        Route::get('/pengajuan/user/{id}', 'getById');
        Route::put('/pengajuan/{id}/status', 'updateStatus');
    });

    // ========== DASHBOARD ==========
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ========== BROADCAST ==========
    Route::post('/send-broadcast', [BroadcastController::class, 'sendBroadcast']);

    // ========== SETTING ==========
    Route::controller(SettingController::class)->group(function () {
        Route::post('/update-lokasi', 'UpdateLokasi');
        Route::get('/lokasi', 'getLokasi');
        Route::post('/pengingat-email/update'. 'updateReminderEmail');
    });

    
    // ========== JADWAL KERJA ==========
    Route::controller(JadwalKerjaController::class)->group(function () {
        Route::get('/jadwal-kerja', 'index');
        Route::post('/jadwal-kerja', 'store');
        Route::get('/jadwal-kerja/{hari}', 'show');
        Route::delete('/jadwal-kerja/{hari}', 'destroy');
    });


Route::get('/pengingat-absen', [PengingatAbsenController::class, 'index']);
Route::post('/pengingat-absen', [PengingatAbsenController::class, 'updateOrCreate']);
Route::post('/pengingat-absen/kirim-ulang', [PengingatAbsenController::class, 'kirimUlang']);

Route::get('/pesan-pengingat', [PesanPengingatController::class, 'get']);
Route::post('/pesan-pengingat', [PesanPengingatController::class, 'update']);


    // ========== LOGOUT ==========
    Route::post('/logout', [AuthController::class, 'logout']);
});
