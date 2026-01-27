<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\SimpananController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/anggota', [AnggotaController::class, 'index'])
            ->name('admin.anggota.index');

        Route::get('/anggota/{anggota}', [AnggotaController::class, 'show'])
            ->name('admin.anggota.show');

        Route::get('/anggota/{anggota}/simpanan/create', [SimpananController::class, 'create'])
            ->name('admin.simpanan.create');

        Route::post('/anggota/{anggota}/simpanan', [SimpananController::class, 'store'])
            ->name('admin.simpanan.store');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
