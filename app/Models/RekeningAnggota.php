<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningAnggota extends Model
{
    protected $fillable = [
        'anggota_id',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik',
        'aktif',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
