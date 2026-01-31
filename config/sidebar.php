<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UMUM
    |--------------------------------------------------------------------------
    */

    [
        'label' => 'Dashboard',
        'route' => 'dashboard',
        'icon'  => 'home',
        'roles' => ['anggota', 'ketua', 'bendahara'],
    ],

    [
        'label' => 'Anggota',
        'route' => 'admin.anggota.index',
        'icon'  => 'users',
        'active_routes' => ['admin.anggota.*'],
        'roles' => ['anggota', 'ketua', 'bendahara'],
        // anggota hanya lihat list, aksi dibatasi di blade/controller
    ],

    /*
    |--------------------------------------------------------------------------
    | KEUANGAN (KETUA & BENDAHARA)
    |--------------------------------------------------------------------------
    */

    [
        'label' => 'Saldo',
        'route' => 'admin.keuangan.saldo',
        'icon'  => 'wallet',
        'roles' => ['ketua', 'bendahara'],
    ],

    [
        'label' => 'Arus Koperasi',
        'route' => 'admin.keuangan.arus.koperasi',
        'icon'  => 'chart-bar',
        'roles' => ['ketua', 'bendahara'],
    ],

    [
        'label' => 'Arus Operasional',
        'route' => 'admin.keuangan.arus.operasional',
        'icon'  => 'chart-bar',
        'roles' => ['ketua', 'bendahara'],
    ],

    /*
    |--------------------------------------------------------------------------
    | KETUA
    |--------------------------------------------------------------------------
    */

    [
        'label' => 'Persetujuan Pinjaman',
        'route' => 'admin.pinjaman.pengajuan.index',
        'icon'  => 'check-circle',
        'roles' => ['ketua'],
    ],

    /*
    |--------------------------------------------------------------------------
    | BENDAHARA
    |--------------------------------------------------------------------------
    */

    [
        'label' => 'Simpanan',
        'route' => 'admin.simpanan.index',
        'icon'  => 'wallet',
        'active_routes' => ['admin.simpanan.*'],
        'roles' => ['bendahara'],
    ],

    [
        'label' => 'Pinjaman',
        'route' => 'admin.pinjaman.pencairan.index',
        'icon'  => 'credit-card',
        'active_routes' => ['admin.pinjaman.*'],
        'roles' => ['bendahara'],
    ],

    /*
    |--------------------------------------------------------------------------
    | AREA ANGGOTA
    |--------------------------------------------------------------------------
    */

    [
        'label' => 'Simpanan Saya',
        'route' => 'anggota.simpanan.index',
        'icon'  => 'wallet',
        'roles' => ['anggota', 'ketua', 'bendahara'],
    ],

    [
        'label' => 'Pinjaman Saya',
        'route' => 'anggota.pinjaman.index',
        'icon'  => 'credit-card',
        'roles' => ['anggota', 'ketua', 'bendahara'],
    ],

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    [
        'label' => 'Profile',
        'route' => 'profile.edit',
        'icon'  => 'user',
        'active_routes' => ['profile.*'],
        'roles' => ['anggota', 'ketua', 'bendahara'],
    ],

];
