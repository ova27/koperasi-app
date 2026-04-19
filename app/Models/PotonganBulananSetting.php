<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotonganBulananSetting extends Model
{
    protected $fillable = [
        'bulan_potongan',
        'iuran_dharma_wanita',
        'infaq_pegawai',
        'tabungan_qurban',
        'is_fixed',
        'fixed_at',
        'fixed_by',
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
        'fixed_at' => 'datetime',
    ];
}
