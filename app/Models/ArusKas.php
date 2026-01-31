<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArusKas extends Model
{
    protected $table = 'arus_kas';

    protected $fillable = [
        'tanggal',
        'rekening_koperasi_id',
        'jenis_arus',
        'tipe',
        'kategori',
        'sub_kategori',
        'jumlah',
        'anggota_id',
        'created_by',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
    
    // RELASI (belum dipakai, tapi aman)
    public function rekening()
    {
        return $this->belongsTo(RekeningKoperasi::class, 'rekening_koperasi_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

