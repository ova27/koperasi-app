<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('potongan_bulanan_details', function (Blueprint $table) {
            $table->string('metode_pembayaran', 20)
                ->default('potong_bank')
                ->after('nomor_rekening')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('potongan_bulanan_details', function (Blueprint $table) {
            $table->dropIndex(['metode_pembayaran']);
            $table->dropColumn('metode_pembayaran');
        });
    }
};
