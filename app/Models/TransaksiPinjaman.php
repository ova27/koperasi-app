<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPinjaman extends Model
{
    protected $table = 'transaksi_pinjaman';
    protected $casts = [
        'tanggal' => 'date',
    ];

    protected $fillable = [
        'pinjaman_id',
        'tanggal',
        'jenis',
        'jumlah',
        'keterangan',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
