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
            if (! Schema::hasColumn('rekening_koperasis', 'nomor_rekening')) {
                $table->string('nomor_rekening', 100)->nullable()->after('nama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekening_koperasis', function (Blueprint $table) {
            if (Schema::hasColumn('rekening_koperasis', 'nomor_rekening')) {
                $table->dropColumn('nomor_rekening');
            }
        });
    }
};
