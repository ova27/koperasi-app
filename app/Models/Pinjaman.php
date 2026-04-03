<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjamans';

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tenor' => 'integer',
    ];

    protected $fillable = [
        'anggota_id',
        'tanggal_pinjam',
        'jumlah_pinjaman',
        'sisa_pinjaman',
        'tenor',
        'cicilan_per_bulan',
        'status',
        'keterangan',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiPinjaman::class);
    }
}
