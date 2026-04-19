<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('potongan_bulanan_settings', function (Blueprint $table) {
            $table->boolean('is_fixed')->default(false)->after('tabungan_qurban');
            $table->timestamp('fixed_at')->nullable()->after('is_fixed');
            $table->foreignId('fixed_by')->nullable()->after('fixed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('potongan_bulanan_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fixed_by');
            $table->dropColumn(['is_fixed', 'fixed_at']);
        });
    }
};
