<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjamans';
    protected $fillable = [
        'anggota_id',
        'tanggal_pinjam',
        'jumlah_pinjaman',
        'sisa_pinjaman',
        'status',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function transaksis()
    {
        return $this->hasMany(TransaksiPinjaman::class);
    }
}
