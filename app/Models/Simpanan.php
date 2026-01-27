<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $fillable = [
        'anggota_id',
        'tanggal',
        'jenis_simpanan',
        'jumlah',
        'sumber',
        'alasan',
        'keterangan',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
