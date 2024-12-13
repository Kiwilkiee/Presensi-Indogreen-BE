<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiTodayController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => 'guest'], function () {
    Route::post('/login', [AuthController::class, 'prosesLogin']);
});

Route::controller(PresensiController::class)->group(function () {

    Route::post('presensi-masuk', 'presensiMasuk');

    Route::post('presensi-pulang', 'presensiPulang');

    Route::get('presensi', 'index');

    Route::get('presensi/{user_id}', 'PresensiHariIni');

    Route::post('presensi/rekap', 'RekapPresensiByDate');

});

Route::resource('user', UserController::class);

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('/pengajuan', [PengajuanController::class, 'store']);
    
    Route::get('/profile', [UserController::class, 'profile']);

    Route::get('/history/{user_id}', [AbsensiController::class, 'getAbsensiById']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::controller(AbsensiController::class)->group(function () {
        
        Route::get('absensi',  'index');
    
        Route::get('absensi/{user_id}',  'getAbsensiById');
    
        Route::get('absensi/today',  'getAbsensiToday');
    
        Route::post('absensi/masuk',  'absenMasuk');
    
        Route::post('absensi/pulang',  'absenPulang');
    
        Route::get('absensi/rekap',  'getRekapByDate'); 
    
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/today', [AbsensiTodayController::class, 'getAbsensiToday']);
   

    
});
