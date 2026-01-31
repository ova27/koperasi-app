<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pengajuan_pinjaman', function (Blueprint $table) {
            // Tenor dalam hitungan bulan
            $table->integer('tenor')->after('jumlah_diajukan')->default(12); 
            // Rencana atau Realisasi bulan pinjam
            $table->string('bulan_pinjam')->after('tenor')->nullable(); 
        });
    }

    public function down()
    {
        Schema::table('pengajuan_pinjaman', function (Blueprint $table) {
            $table->dropColumn(['tenor', 'bulan_pinjam']);
        });
    }
};
