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
        Schema::create('closing_bulans', function (Blueprint $table) {
            $table->id();
            $table->string('bulan'); // format: YYYY-MM
            $table->string('jenis'); // simpanan | pinjaman
            $table->unsignedBigInteger('ditutup_oleh')->nullable();
            $table->timestamp('ditutup_pada')->nullable();
            $table->timestamps();

            $table->unique(['bulan', 'jenis']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closing_bulans');
    }
};
