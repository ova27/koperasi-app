<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengajuan_pinjaman', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anggota_id')
                  ->constrained('anggotas')
                  ->cascadeOnDelete();

            $table->integer('jumlah_diajukan');
            $table->string('tujuan')->nullable();

            $table->enum('status', [
                'diajukan',
                'disetujui',
                'ditolak',
                'dibatalkan',
                'dicairkan'
            ])->default('diajukan');

            $table->foreignId('diajukan_oleh')
                  ->constrained('users');

            $table->foreignId('disetujui_oleh')
                  ->nullable()
                  ->constrained('users');

            $table->foreignId('dicairkan_oleh')
                  ->nullable()
                  ->constrained('users');

            $table->date('tanggal_pengajuan');
            $table->date('tanggal_persetujuan')->nullable();
            $table->date('tanggal_pencairan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pinjaman');
    }
};
