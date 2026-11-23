<?php

namespace App\Http\Controllers;

use App\Models\UndanganAktif;
use App\Models\Galeri;
use App\Models\Ucapan;

class UndanganController extends Controller
{
    public function index()
    {
        $akad = UndanganAktif::acaraAktif();
        if (! $akad) {
            return view('undangan-belum-aktif');
        }
        session(['undangan_acara_id' => $akad->id]);
        $mempelai = $akad->mempelai;

        $galeris = Galeri::where('mempelai_id', $mempelai?->id)
            ->latest()
            ->get();

        $ucapans = Ucapan::with('tamu')
            ->where('acara_id', $akad->id)
            ->where('is_approved', true)
            ->latest()->orderByDesc('id')
            ->simplePaginate(10, ['*'], 'buku')->withQueryString()->fragment('ucapan');

        return view('undangan', compact(
            'mempelai',
            'akad',
            'galeris',
            'ucapans'
        ));
    }
}
