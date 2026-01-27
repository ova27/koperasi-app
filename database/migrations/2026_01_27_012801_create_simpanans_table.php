<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('simpanans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anggota_id')
                  ->constrained('anggotas')
                  ->cascadeOnDelete();

            $table->date('tanggal');

            $table->enum('jenis_simpanan', [
                'pokok',
                'wajib',
                'sukarela'
            ]);

            $table->integer('jumlah');
            // positif = simpan
            // negatif = ambil / pengembalian

            $table->enum('sumber', [
                'manual',
                'otomatis',
                'saldo_awal',
                'pengembalian'
            ]);

            $table->enum('alasan', [
                'biasa',
                'rutin',
                'pensiun',
                'mutasi',
                'koreksi'
            ])->default('biasa');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanans');
    }
};
