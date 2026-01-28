<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\JenisPeralatanController;
use Illuminate\Support\Facades\Route;

// Tampilkan halaman login
Route::get('/', [AuthController::class, 'login'])->name('login');

// Proses login (POST)
Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');

// Dashboard
Route::get('/main', [MainController::class, 'index'])
    ->middleware('auth')
    ->name('main');

// Log Activity
Route::get('/logactivity', [AuthController::class, 'logactivity'])
    ->middleware('auth')
    ->name('logactivity');


// Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

//Activity Log
Route::get('/activity-log', function () {
    return view('activity.index');
})->middleware('auth')->name('activity.log');
//jenis peralatan

Route::get('/jenis-peralatan', [JenisPeralatanController::class, 'index'])
    ->middleware('auth')
    ->name('jenisperalatan');

Route::get('/tambahperalatan', [JenisPeralatanController::class, 'create'])
    ->middleware('auth')
    ->name('tambahperalatan');

Route::post('/tambahperalatan', [JenisPeralatanController::class, 'store'])
    ->middleware('auth')
    ->name('tambahperalatan.store');

// Tampilkan form edit
Route::get('/jenis-peralatan/{id}/edit', [JenisPeralatanController::class, 'edit'])
    ->name('tambahperalatan.edit');

// Update
Route::put('/jenis-peralatan/{id}', [JenisPeralatanController::class, 'update'])
    ->name('tambahperalatan.update');
Route::delete('/jenis-peralatan/{id}', [JenisPeralatanController::class, 'destroy'])
    ->name('tambahperalatan.destroy');

