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
        Schema::table('transaksi_pinjaman', function (Blueprint $table) {
            $table->bigInteger('sisa_setelah')->nullable()->after('jumlah');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_pinjaman', function (Blueprint $table) {
            //
        });
    }
};
