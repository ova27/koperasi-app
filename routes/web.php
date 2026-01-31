<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{
    AnggotaController,
    SimpananController,
    AnggotaExitController,
    LaporanPinjamanController,
    LaporanSimpananController,
    GenerateSimpananWajibController,
    ApprovalPinjamanController,
    PencairanPinjamanController,
    CicilanPinjamanController,
    SaldoController,
    ArusKasKoperasiController,
    ArusOperasionalController,
    LaporanArusKasController
};
use App\Http\Controllers\Anggota\{
    SimpananSayaController,
    PinjamanSayaController,
    PengajuanPinjamanController
};

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| DASHBOARD (SEMUA ROLE)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN - KEUANGAN
| KETUA & BENDAHARA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:ketua,bendahara'])
    ->prefix('admin/keuangan')
    ->name('admin.keuangan.')
    ->group(function () {

        Route::get('/saldo', [SaldoController::class, 'index'])
            ->name('saldo');

        Route::get('/arus-koperasi', [ArusKasKoperasiController::class, 'index'])
            ->name('arus.koperasi');

        Route::get('/arus-operasional', [ArusOperasionalController::class, 'index'])
            ->name('arus.operasional');

        Route::get('/arus-kas', [LaporanArusKasController::class, 'index'])
            ->name('arus-kas');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - UMUM (SEMUA ROLE LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        | ANGGOTA
        | Semua role boleh lihat list
        | Aksi dibatasi di controller / blade
        */
        Route::get('/anggota', [AnggotaController::class, 'index'])
            ->name('anggota.index');

        Route::get('/anggota/{anggota}', [AnggotaController::class, 'show'])
            ->middleware('role:ketua,bendahara')
            ->name('anggota.show');

        /*
        | SIMPANAN PER ANGGOTA (BENDAHARA)
        */
        Route::middleware('role:bendahara')->group(function () {

            Route::get('/anggota/{anggota}/simpanan/create', [SimpananController::class, 'create'])
                ->name('simpanan.create');

            Route::post('/anggota/{anggota}/simpanan', [SimpananController::class, 'store'])
                ->name('simpanan.store');

            Route::post('/anggota/{anggota}/simpanan/ambil', [SimpananController::class, 'ambil'])
                ->name('simpanan.ambil');

            Route::get('/anggota/{anggota}/keluar', [AnggotaExitController::class, 'confirm'])
                ->name('anggota.keluar.confirm');

            Route::post('/anggota/{anggota}/keluar', [AnggotaExitController::class, 'process'])
                ->name('anggota.keluar.process');
        });

        /*
        | PERSETUJUAN PINJAMAN (KETUA)
        */
        Route::middleware('role:ketua')->group(function () {

            Route::get('/pinjaman/pengajuan', [ApprovalPinjamanController::class, 'index'])
                ->name('pinjaman.pengajuan.index');

            Route::get('/pinjaman/pengajuan/{pengajuan}', [ApprovalPinjamanController::class, 'show'])
                ->name('pinjaman.pengajuan.show');

            Route::post('/pinjaman/pengajuan/{pengajuan}/setujui', [ApprovalPinjamanController::class, 'setujui'])
                ->name('pinjaman.pengajuan.setujui');

            Route::post('/pinjaman/pengajuan/{pengajuan}/tolak', [ApprovalPinjamanController::class, 'tolak'])
                ->name('pinjaman.pengajuan.tolak');
        });

        /*
        | BENDAHARA
        */
        Route::middleware('role:bendahara')->group(function () {

            // SIMPANAN
            Route::get('/simpanan', [SimpananController::class, 'index'])
                ->name('simpanan.index');

            Route::post('/simpanan/manual', [SimpananController::class, 'storeManual'])
                ->name('simpanan.store-manual');

            Route::get('/simpanan/generate-wajib', [GenerateSimpananWajibController::class, 'index'])
                ->name('simpanan.generate-wajib');

            Route::post('/simpanan/generate-wajib', [GenerateSimpananWajibController::class, 'process'])
                ->name('simpanan.generate-wajib.process');

            // PINJAMAN
            Route::get('/pinjaman/pencairan', [PencairanPinjamanController::class, 'index'])
                ->name('pinjaman.pencairan.index');

            Route::post('/pinjaman/pencairan/{pengajuan}', [PencairanPinjamanController::class, 'process'])
                ->name('pinjaman.pencairan.process');

            Route::get('/pinjamanjaman/aktif', [CicilanPinjamanController::class, 'index'])
                ->name('pinjaman.aktif.index');

            Route::get('/pinjaman/{pinjaman}/cicil', [CicilanPinjamanController::class, 'create'])
                ->name('pinjaman.cicil.create');

            Route::post('/pinjaman/{pinjaman}/cicil', [CicilanPinjamanController::class, 'store'])
                ->name('pinjaman.cicil.store');

            // LAPORAN
            Route::get('/laporan/simpanan-bulanan', [LaporanSimpananController::class, 'index'])
                ->name('laporan.simpanan-bulanan');

            Route::get('/laporan/simpanan-bulanan/export', [LaporanSimpananController::class, 'export'])
                ->name('laporan.simpanan-bulanan.export');

            Route::post('/laporan/simpanan-bulanan/lock', [LaporanSimpananController::class, 'lock'])
                ->name('laporan.simpanan-bulanan.lock');

            Route::get('/laporan/pinjaman', [LaporanPinjamanController::class, 'index'])
                ->name('laporan.pinjaman.index');

            Route::get('/laporan/pinjaman/export', [LaporanPinjamanController::class, 'export'])
                ->name('laporan.pinjaman.export');

            Route::get('/laporan/pinjaman/{pinjaman}', [LaporanPinjamanController::class, 'show'])
                ->name('laporan.pinjaman.show');
        });
    });

/*
|--------------------------------------------------------------------------
| ANGGOTA AREA (SEMUA LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('anggota')
    ->name('anggota.')
    ->group(function () {

        Route::get('/pinjaman/ajukan', [PengajuanPinjamanController::class, 'create'])
            ->name('pinjaman.ajukan');

        Route::post('/pinjaman/ajukan', [PengajuanPinjamanController::class, 'store'])
            ->name('pinjaman.store');

        Route::get('/pinjaman/{pengajuan}/edit', [PengajuanPinjamanController::class, 'edit'])
            ->name('pinjaman.edit');

        Route::put('/pinjaman/{pengajuan}', [PengajuanPinjamanController::class, 'update'])
            ->name('pinjaman.update');

        Route::delete('/pinjaman/{id}', [PengajuanPinjamanController::class, 'destroy'])
            ->name('pinjaman.destroy');

        Route::get('/simpanan', [SimpananSayaController::class, 'index'])
            ->name('simpanan.index');

        Route::get('/pinjaman', [PinjamanSayaController::class, 'index'])
            ->name('pinjaman.index');
    });

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
