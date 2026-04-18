<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotonganTitipan extends Model
{
    protected $fillable = [
        'anggota_id',
        'iuran_dharma_wanita',
        'infaq_pegawai',
        'tabungan_qurban',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
