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
        Schema::create('rekening_koperasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Kas Tunai, BCA, BRI
            $table->enum('jenis', ['kas', 'bank']);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening_koperasis');
    }
};
