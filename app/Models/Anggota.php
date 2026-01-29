<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $fillable = [
        'user_id',
        'nomor_anggota',
        'nip',
        'nama',
        'email',
        'status',
        'tanggal_masuk',
        'tanggal_keluar',
    ];

    public function rekening()
    {
        return $this->hasMany(RekeningAnggota::class);
    }

    public function rekeningAktif()
    {
        return $this->hasOne(RekeningAnggota::class)
                    ->where('aktif', true);
    }

    public function simpanans()
    {
        return $this->hasMany(Simpanan::class);
    }

    public function pinjamans()
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function pengajuanPinjamans()
    {
        return $this->hasMany(PengajuanPinjaman::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
