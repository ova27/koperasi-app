<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 
    ];

    public function boot(): void
    {
        // 🔹 LAPORAN SIMPANAN
        Gate::define('lihat-laporan-simpanan', fn ($user) =>
            $user->hasRole('admin') ||
            $user->hasRole('bendahara') ||
            $user->hasRole('ketua')
        );

        // 🔹 LAPORAN PINJAMAN
        Gate::define('lihat-laporan-pinjaman', fn ($user) =>
            $user->hasRole('admin') ||
            $user->hasRole('bendahara') ||
            $user->hasRole('ketua')
        );

        // 🔒 KEUANGAN GLOBAL (TIDAK untuk anggota)
        Gate::define('lihat-keuangan-global', fn ($user) =>
            $user->hasRole('admin') ||
            $user->hasRole('bendahara') ||
            $user->hasRole('ketua')
        );
    }

}
