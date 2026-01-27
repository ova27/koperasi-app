<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi_pinjaman', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pinjaman_id')
                  ->constrained('pinjamans')
                  ->cascadeOnDelete();

            $table->date('tanggal');

            $table->enum('jenis', [
                'cicilan',
                'topup',
                'pelunasan'
            ]);

            $table->integer('jumlah');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pinjaman');
    }
};
