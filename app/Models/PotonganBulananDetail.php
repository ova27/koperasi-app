<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotonganBulananDetail extends Model
{
    protected $fillable = [
        'bulan_potongan',
        'anggota_id',
        'nama',
        'bank',
        'nomor_rekening',
        'simpanan_wajib',
        'simpanan_sukarela',
        'cicilan',
        'iuran_dharma_wanita',
        'infaq_pegawai',
        'tabungan_qurban',
        'total_titipan',
        'iuran_operasional',
        'total',
        'sisa_pinjaman_lalu',
        'sisa_pinjaman_sekarang',
        'tenor',
        'cicilan_ke',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
