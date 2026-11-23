<?php

namespace App\Http\Controllers;

use App\Models\UndanganAktif;
use App\Models\Rsvp;
use App\Models\Tamu;
use App\Models\Ucapan;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RsvpController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:hadir,tidak_hadir'],
            'jumlah_hadir' => ['required', 'integer', 'between:1,20'],
            'pesan' => ['nullable', 'string', 'max:1000'],
            'submission_id' => ['nullable', 'uuid'],
        ]);

        $acara = UndanganAktif::untukKiriman($request->input('acara_id', $request->session()->get('undangan_acara_id')));
        $key = 'rsvp:'.$acara->id.':'.hash('sha256', (string) ($data['submission_id'] ?? Str::uuid()));
        try {
            $message = Cache::lock($key.':lock', 30)->block(5, function () use ($data, $key, $acara) {
                return Cache::remember($key, now()->addDay(), function () use ($data, $acara) {
                    return DB::transaction(function () use ($data, $acara) {
                        UndanganAktif::untukKiriman($acara->id, true);
                        $tamu = Tamu::firstOrCreate(['nama' => $data['nama']], ['kode_undangan' => (string) Str::uuid()]);
                        Rsvp::updateOrCreate(
                            ['tamu_id' => $tamu->id, 'acara_id' => $acara->id],
                            ['status' => $data['status'], 'jumlah_hadir' => $data['jumlah_hadir']]
                        );

                        if (filled($data['pesan'] ?? null)) {
                            Ucapan::create(['acara_id' => $acara->id, 'tamu_id' => $tamu->id, 'pesan' => $data['pesan'], 'is_approved' => false]);

                            return 'Konfirmasi tersimpan. Ucapan Anda akan tampil setelah disetujui.';
                        }

                        return 'Konfirmasi kehadiran berhasil dikirim!';
                    });
                });
            });
        } catch (LockTimeoutException) {
            throw ValidationException::withMessages(['form' => 'Pengiriman sedang diproses. Silakan coba kembali.']);
        }

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'submission_id' => (string) Str::uuid()])
            : back()->with('success', $message);
    }
}
