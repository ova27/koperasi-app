<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anggota_id')
                  ->constrained('anggotas')
                  ->cascadeOnDelete();

            $table->date('tanggal_pinjam');
            $table->integer('jumlah_pinjaman');
            $table->integer('sisa_pinjaman');

            $table->enum('status', ['aktif', 'lunas'])
                  ->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};
