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
        Schema::create('arus_kas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');

            $table->foreignId('rekening_koperasi_id')
                ->constrained('rekening_koperasis');

            $table->enum('jenis_arus', ['operasional', 'koperasi']);
            $table->enum('tipe', ['masuk', 'keluar']);

            $table->string('kategori');      // simpanan, pinjaman, iuran
            $table->string('sub_kategori')->nullable(); // wajib, pokok, angsuran

            $table->decimal('jumlah', 15, 2);

            $table->foreignId('anggota_id')
                ->nullable()
                ->constrained('anggotas');

            $table->text('keterangan')->nullable();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arus_kas');
    }
};
