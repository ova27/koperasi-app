<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('potongan_bulanan_settings', function (Blueprint $table) {
            $table->id();
            $table->string('bulan_potongan', 7)->unique();
            $table->unsignedBigInteger('iuran_dharma_wanita')->default(0);
            $table->unsignedBigInteger('infaq_pegawai')->default(0);
            $table->unsignedBigInteger('tabungan_qurban')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potongan_bulanan_settings');
    }
};
