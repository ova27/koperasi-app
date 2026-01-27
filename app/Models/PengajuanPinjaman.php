<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPinjaman extends Model
{
    protected $table = 'pengajuan_pinjaman';

    protected $fillable = [
        'anggota_id',
        'jumlah_diajukan',
        'tujuan',
        'status',
        'diajukan_oleh',
        'disetujui_oleh',
        'dicairkan_oleh',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
        'tanggal_pencairan',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pencair()
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }
}
