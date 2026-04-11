<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::firstOrCreate(['name' => 'view anggota list']);
        $admin = Role::where('name', 'admin')->first();

        if ($admin) {
            $admin->givePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::where('name', 'admin')->first();

        if ($admin) {
            $admin->revokePermissionTo('view anggota list');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
