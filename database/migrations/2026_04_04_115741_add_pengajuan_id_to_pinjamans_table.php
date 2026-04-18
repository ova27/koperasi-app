<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pinjamans') || Schema::hasColumn('pinjamans', 'pengajuan_id')) {
            return;
        }

        Schema::table('pinjamans', function (Blueprint $table) {
            $table->foreignId('pengajuan_id')
                ->after('id')
                ->constrained('pengajuan_pinjaman')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left empty to avoid dropping existing production data.
    }
};
