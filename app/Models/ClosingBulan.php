<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosingBulan extends Model
{
    protected $fillable = [
        'bulan',
        'jenis',
        'ditutup_oleh',
        'ditutup_pada',
    ];
}