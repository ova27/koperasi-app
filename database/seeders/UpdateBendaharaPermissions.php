<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        \Artisan::call('tinker', [
            '--execute' => <<<'PHP'
$bendahara = \Spatie\Permission\Models\Role::where('name', 'bendahara')->first();
if ($bendahara) {
    $bendahara->givePermissionTo('view pengajuan pinjaman');
    echo "✓ Bendahara diberi permission 'view pengajuan pinjaman'";
}
PHP
        ]);
    }
};
