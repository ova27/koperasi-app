<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\SimpananController;
use App\Http\Controllers\Admin\AnggotaExitController;
use App\Http\Controllers\Admin\GenerateSimpananWajibController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/anggota', [AnggotaController::class, 'index'])
            ->name('anggota.index');

        Route::get('/anggota/{anggota}', [AnggotaController::class, 'show'])
            ->name('anggota.show');

        Route::get('/anggota/{anggota}/simpanan/create', [SimpananController::class, 'create'])
            ->name('simpanan.create');

        Route::post('/anggota/{anggota}/simpanan', [SimpananController::class, 'store'])
            ->name('simpanan.store');

        Route::post('/anggota/{anggota}/simpanan/ambil', [SimpananController::class, 'ambil'])
            ->name('simpanan.ambil');
        
        // halaman konfirmasi
        Route::get('/anggota/{anggota}/keluar', [AnggotaExitController::class, 'confirm'])
            ->name('anggota.keluar.confirm');

        // eksekusi pensiun / mutasi
        Route::post('/anggota/{anggota}/keluar', [AnggotaExitController::class, 'process'])
            ->name('anggota.keluar.process');

        Route::get('/simpanan/generate-wajib', [GenerateSimpananWajibController::class, 'index'])
            ->name('simpanan.generate-wajib');

        Route::post('/simpanan/generate-wajib', [GenerateSimpananWajibController::class, 'process'])
            ->name('simpanan.generate-wajib.process');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
