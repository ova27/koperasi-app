<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningKoperasi extends Model
{
    protected $table = 'rekening_koperasis';

    protected $fillable = ['nama', 'nomor_rekening', 'nama_pemilik', 'jenis', 'aktif'];

    public function arusKas()
    {
        return $this->hasMany(ArusKas::class);
    }
}

