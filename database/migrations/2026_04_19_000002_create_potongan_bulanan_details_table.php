<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('potongan_bulanan_details', function (Blueprint $table) {
            $table->id();
            $table->string('bulan_potongan', 7)->index();
            $table->foreignId('anggota_id')->nullable()->constrained('anggotas')->nullOnDelete();
            $table->string('nama');
            $table->string('bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->unsignedBigInteger('simpanan_wajib')->default(0);
            $table->unsignedBigInteger('cicilan')->default(0);
            $table->unsignedBigInteger('iuran_dharma_wanita')->default(0);
            $table->unsignedBigInteger('infaq_pegawai')->default(0);
            $table->unsignedBigInteger('tabungan_qurban')->default(0);
            $table->unsignedBigInteger('total_titipan')->default(0);
            $table->unsignedBigInteger('iuran_operasional')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('sisa_pinjaman_lalu')->default(0);
            $table->unsignedBigInteger('sisa_pinjaman_sekarang')->default(0);
            $table->string('tenor')->nullable();
            $table->string('cicilan_ke')->nullable();
            $table->timestamps();

            $table->unique(['bulan_potongan', 'anggota_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potongan_bulanan_details');
    }
};
