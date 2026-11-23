<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ucapan;
use App\Models\UndanganAktif;
use Illuminate\Support\Facades\DB;
use App\Models\Tamu;
use Illuminate\Support\Str;

class UcapanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'pesan' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($request) {
            $acara = UndanganAktif::untukKiriman($request->input('acara_id', $request->session()->get('undangan_acara_id')), true);
            $tamu = Tamu::firstOrCreate(
                ['nama' => $request->nama],
                ['kode_undangan' => (string) Str::uuid()]
            );
            Ucapan::create([
                'acara_id' => $acara->id,
                'tamu_id' => $tamu->id,
                'pesan' => $request->pesan,
                'is_approved' => false,
            ]);
        });

        return back()->with(
            'success_ucapan',
            'Terima kasih, ucapan Anda berhasil dikirim!'
        );
    }
}