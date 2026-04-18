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
        Schema::table('rekening_koperasis', function (Blueprint $table) {
            if (! Schema::hasColumn('rekening_koperasis', 'nama_pemilik')) {
                $table->string('nama_pemilik', 255)->nullable()->after('nomor_rekening');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekening_koperasis', function (Blueprint $table) {
            if (Schema::hasColumn('rekening_koperasis', 'nama_pemilik')) {
                $table->dropColumn('nama_pemilik');
            }
        });
    }
};
