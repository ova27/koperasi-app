<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('simpanans', function (Blueprint $table) {
            $table->string('alasan', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('simpanans', function (Blueprint $table) {
            $table->enum('alasan', ['biasa', 'koreksi'])->change();
        });
    }
};
