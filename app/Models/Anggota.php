<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PotonganTitipan;

class Anggota extends Model
{
    protected $fillable = [
        'user_id',
        'nomor_anggota',
        'nip',
        'nama',
        'jenis_kelamin',
        'jabatan',
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
        return $this->hasMany(PengajuanPinjaman::class, 'anggota_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pinjamanAktif()
    {
        return $this->hasOne(Pinjaman::class)
            ->where('status', 'aktif'); // sesuaikan dengan field kamu
    }

    public function potonganTitipan()
    {
        return $this->hasOne(PotonganTitipan::class);
    }
    
    public function potonganBulananDetail()
    {
        return $this->hasMany(\App\Models\PotonganBulananDetail::class, 'anggota_id');
    }
}
