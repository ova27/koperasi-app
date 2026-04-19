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
        Schema::table('potongan_bulanan_details', function (Blueprint $table) {
            $table->bigInteger('simpanan_sukarela')->default(0);
        });
    }

    public function down()
    {
        Schema::table('potongan_bulanan_details', function (Blueprint $table) {
            $table->dropColumn('simpanan_sukarela');
        });
    }
};
