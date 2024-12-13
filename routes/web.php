<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('auth.login')->name('login');
// });

Route::middleware(['guest'])->group(function () {
    
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'prosesLogin'])->name('prosesLogin');
    
});



Route::group(['middleware' => 'auth'], function () { 
    
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

});