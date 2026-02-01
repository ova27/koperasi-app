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
        Schema::table('anggotas', function (Blueprint $table) {
            $table->char('jenis_kelamin', 1)
                ->nullable()
                ->after('nama');

            $table->string('jabatan')
                ->nullable()
                ->after('jenis_kelamin');
        });
    }

    public function down()
    {
        Schema::table('anggotas', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'jabatan']);
        });
    }

};
