<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{
    AnggotaController,
    UserController,
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
Route::middleware(['auth','permission:view dashboard'])
    ->get('/dashboard', fn () => view('dashboard'))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| KEUANGAN (KETUA & BENDAHARA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','permission:view saldo'])
    ->prefix('admin/keuangan')
    ->name('admin.keuangan.')
    ->group(function () {

        Route::get('/saldo', [SaldoController::class, 'index'])
            ->name('saldo');

        Route::middleware('permission:view arus koperasi')
            ->get('/arus-koperasi', [ArusKasKoperasiController::class, 'index'])
            ->name('arus.koperasi');

        Route::middleware('permission:view arus koperasi')
            ->get('/arus-koperasi/export', [ArusKasKoperasiController::class, 'export'])
            ->name('arus.koperasi.export');

        Route::middleware('permission:view arus operasional')
            ->get('/arus-operasional', [ArusOperasionalController::class, 'index'])
            ->name('arus.operasional');

        Route::middleware('permission:view arus operasional')
            ->get('/arus-operasional/export', [ArusOperasionalController::class, 'export'])
            ->name('arus.operasional.export');

        Route::middleware('permission:view laporan arus kas')
            ->get('/arus-kas', [LaporanArusKasController::class, 'index'])
            ->name('arus-kas');
        Route::middleware('permission:export laporan arus kas')
            ->get('laporan/arus-kas/export', [LaporanArusKasController::class, 'export'])
            ->name('laporan.arus-kas.export');
    });

// EDIT ANGGOTA
Route::middleware(['auth', 'permission:edit anggota'])
    ->prefix('admin/anggota')
    ->name('admin.anggota.')
    ->group(function () {

        Route::get('{anggota}/edit', [AnggotaController::class, 'edit'])
            ->name('edit');

        Route::put('{anggota}', [AnggotaController::class, 'update'])
            ->name('update');
    });

/*
|--------------------------------------------------------------------------
| ADMIN / DATA UMUM
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','permission:view anggota list'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        | DATA ANGGOTA
        */
        Route::get('/anggota', [AnggotaController::class, 'index'])
            ->name('anggota.index');

        Route::middleware('permission:view anggota list')
            ->get('/anggota/{anggota}', [AnggotaController::class, 'show'])
            ->name('anggota.show');

        // NONAKTIFKAN (cuti / tugas_belajar / tidak_aktif)
        Route::middleware('can:nonaktifkan anggota')
            ->post('{anggota}/nonaktifkan', [AnggotaController::class, 'nonaktifkan'])
            ->name('anggota.nonaktifkan');

        /*
        | SIMPANAN PER ANGGOTA (BENDAHARA)
        */
        Route::middleware('permission:manage simpanan anggota')
            ->group(function () {

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
        | APPROVAL PINJAMAN (KETUA)
        */
        Route::middleware('permission:view pengajuan pinjaman')
            ->group(function () {

                Route::get('/pinjaman/pengajuan', [ApprovalPinjamanController::class, 'index'])
                    ->name('pinjaman.pengajuan.index');

                Route::get('/pinjaman/pengajuan/{pengajuan}', [ApprovalPinjamanController::class, 'show'])
                    ->name('pinjaman.pengajuan.show');

                Route::middleware('permission:approve pinjaman')
                    ->post('/pinjaman/pengajuan/{pengajuan}/setujui', [ApprovalPinjamanController::class, 'setujui'])
                    ->name('pinjaman.pengajuan.setujui');

                Route::middleware('permission:reject pinjaman')
                    ->post('/pinjaman/pengajuan/{pengajuan}/tolak', [ApprovalPinjamanController::class, 'tolak'])
                    ->name('pinjaman.pengajuan.tolak');
            });

        /*
        | TRANSAKSI (BENDAHARA)
        */
        Route::middleware('permission:manage simpanan anggota')
            ->group(function () {

                // SIMPANAN
                Route::get('/simpanan', [SimpananController::class, 'index'])
                    ->name('simpanan.index');

                Route::post('/simpanan/manual', [SimpananController::class, 'storeManual'])
                    ->name('simpanan.store-manual');

                Route::get('/simpanan/generate-wajib', [GenerateSimpananWajibController::class, 'index'])
                    ->name('simpanan.generate-wajib');

                Route::post('/simpanan/generate-wajib', [GenerateSimpananWajibController::class, 'process'])
                    ->name('simpanan.generate-wajib.process');
            });

        /*
        | PINJAMAN (BENDAHARA)
        */
        Route::middleware('permission:pencairan pinjaman')
            ->group(function () {

                Route::get('/pinjaman/pencairan', [PencairanPinjamanController::class, 'index'])
                    ->name('pinjaman.pencairan.index');

                Route::post('/pinjaman/pencairan/{pengajuan}', [PencairanPinjamanController::class, 'process'])
                    ->name('pinjaman.pencairan.process');
            });

        Route::middleware('permission:manage cicilan pinjaman')
            ->group(function () {

                Route::get('/pinjaman/aktif', [CicilanPinjamanController::class, 'index'])
                    ->name('pinjaman.aktif.index');

                Route::get('/pinjaman/{pinjaman}/cicil', [CicilanPinjamanController::class, 'create'])
                    ->name('pinjaman.cicil.create');

                Route::post('/pinjaman/{pinjaman}/cicil', [CicilanPinjamanController::class, 'store'])
                    ->name('pinjaman.cicil.store');
            });

        /*
        | LAPORAN SIMPANAN BULANAN
        */
        Route::middleware('permission:view laporan simpanan bulanan')
            ->group(function () {
                // VIEW → anggota, ketua, bendahara
                Route::get('/laporan/simpanan-bulanan', [LaporanSimpananController::class, 'index'])
                    ->name('laporan.simpanan-bulanan');
        });
        
        /*
        | EXPORT → ketua & bendahara
        */
        Route::middleware(['auth','permission:export laporan simpanan'])
            ->get('/laporan/simpanan-bulanan/export', [LaporanSimpananController::class, 'export'])
            ->name('laporan.simpanan-bulanan.export');

        /*
        | LOCK → bendahara SAJA
        */
        Route::middleware(['auth','permission:lock laporan simpanan'])
            ->post('/laporan/simpanan-bulanan/lock', [LaporanSimpananController::class, 'lock'])
            ->name('laporan.simpanan-bulanan.lock');
                

        Route::middleware('permission:view laporan pinjaman')
            ->group(function () {

                Route::get('/laporan/pinjaman', [LaporanPinjamanController::class, 'index'])
                    ->name('laporan.pinjaman.index');

                Route::middleware('permission:export laporan pinjaman')
                    ->get('/laporan/pinjaman/export', [LaporanPinjamanController::class, 'export'])
                    ->name('laporan.pinjaman.export');

                Route::get('/laporan/pinjaman/{pinjaman}', [LaporanPinjamanController::class, 'show'])
                    ->name('laporan.pinjaman.show');
            });
    });

/*
|--------------------------------------------------------------------------
| AREA ANGGOTA (SEMUA ROLE, DATA SENDIRI)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','permission:view pinjaman saya'])
    ->prefix('anggota')
    ->name('anggota.')
    ->group(function () {

        Route::middleware('permission:create pinjaman')
            ->get('/pinjaman/ajukan', [PengajuanPinjamanController::class, 'create'])
            ->name('pinjaman.ajukan');

        Route::middleware('permission:create pinjaman')
            ->post('/pinjaman/ajukan', [PengajuanPinjamanController::class, 'store'])
            ->name('pinjaman.store');

        Route::middleware('permission:edit pinjaman')
            ->get('/pinjaman/{pengajuan}/edit', [PengajuanPinjamanController::class, 'edit'])
            ->name('pinjaman.edit');

        Route::middleware('permission:edit pinjaman')
            ->put('/pinjaman/{pengajuan}', [PengajuanPinjamanController::class, 'update'])
            ->name('pinjaman.update');

        Route::middleware('permission:delete pinjaman')
            ->delete('/pinjaman/{id}', [PengajuanPinjamanController::class, 'destroy'])
            ->name('pinjaman.destroy');

        Route::get('/simpanan', [SimpananSayaController::class, 'index'])
            ->middleware('permission:view simpanan saya')
            ->name('simpanan.index');

        Route::get('/pinjaman', [PinjamanSayaController::class, 'index'])
            ->name('pinjaman.index');
    });

/*
|--------------------------------------------------------------------------
| MANAGE USERS
|--------------------------------------------------------------------------
*/ 
Route::middleware(['auth', 'permission:manage users'])
    ->prefix('admin/users')
    ->name('admin.users.')
    ->group(function () {

        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');

        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
    });

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','permission:edit profil'])
    ->group(function () {

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

require __DIR__.'/auth.php';
