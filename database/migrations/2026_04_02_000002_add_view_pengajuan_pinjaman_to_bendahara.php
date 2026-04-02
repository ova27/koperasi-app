<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    public function up()
    {
        $bendahara = Role::where('name', 'bendahara')->first();
        if ($bendahara) {
            $bendahara->givePermissionTo('view pengajuan pinjaman');
        }
    }

    public function down()
    {
        $bendahara = Role::where('name', 'bendahara')->first();
        if ($bendahara) {
            $bendahara->revokePermissionTo('view pengajuan pinjaman');
        }
    }
};
