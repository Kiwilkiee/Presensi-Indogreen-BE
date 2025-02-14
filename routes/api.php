<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiTodayController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RekapController;
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

    Route::get('rekap-absensi-user', [AbsensiController::class, 'getRekapWithUserDetails']);


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/today', [AbsensiTodayController::class, 'getAbsensiToday']);

    // Route::get('/absensi/rekap', [AbsensiTodayController::class, 'getRekapByDate']);

    Route::post('/rekap/ByDate', [RekapController::class, 'RekapitDate']);

    Route::get('absensi/hari-ini', [AbsensiController::class, 'getAbsensiHariIni']);

    Route::get('/absensi-with-status', [AbsensiTodayController::class, 'getAbsensiWithStatus']);


    Route::get('/pengajuan', [PengajuanController::class, 'index']);
    Route::post('/pengajuan', [PengajuanController::class, 'store']);
    Route::get('/pengajuan/{id}', [PengajuanController::class, 'show']);
    Route::put('/pengajuan/{id}/status', [PengajuanController::class, 'updateStatus']);


});
