<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilSayaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('edit profil');

        $user = $request->user()->load([
            'anggota.rekeningAktif',
            'anggota.rekening',
        ]);

        return view('anggota.profil.index', [
            'user' => $user,
        ]);
    }
}
