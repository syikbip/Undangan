<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Mempelai;
use App\Models\Tamu;
use App\Models\Ucapan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WeddingGuestbookTest extends TestCase
{
    use RefreshDatabase;

    private function createEvent(): Acara
    {
        $couple = Mempelai::create(['nama_pria' => 'Idham', 'nama_wanita' => 'Ketrin']);
        $event = Acara::create(['mempelai_id' => $couple->id, 'nama_acara' => 'Akad Nikah', 'tanggal' => '2027-12-20',
            'waktu_mulai' => '08:00', 'lokasi' => 'Tempat uji', 'alamat' => 'Alamat uji']);
        \App\Models\UndanganAktif::pilih($event);

        return $event;
    }

    public function test_retried_submission_creates_one_pending_message_and_rsvp_can_be_updated(): void
    {
        $event = $this->createEvent();
        $payload = ['acara_id' => $event->id, 'nama' => 'Rani', 'jumlah_hadir' => 2, 'status' => 'hadir', 'pesan' => 'Selamat!',
            'submission_id' => (string) Str::uuid()];
        $this->postJson('/rsvp', $payload)->assertOk();
        $this->postJson('/rsvp', $payload)->assertOk();
        $this->assertDatabaseCount('rsvps', 1);
        $this->assertDatabaseCount('ucapans', 1);
        $this->assertDatabaseHas('ucapans', ['pesan' => 'Selamat!', 'is_approved' => false]);

        $this->post('/rsvp', ['acara_id' => $event->id, 'nama' => 'Rani', 'status' => 'tidak_hadir', 'jumlah_hadir' => 1, 'pesan' => '  '])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseCount('ucapans', 1);
        $this->assertDatabaseHas('rsvps', ['status' => 'tidak_hadir', 'jumlah_hadir' => 1]);
    }

    public function test_invalid_input_and_missing_event_do_not_create_records(): void
    {
        foreach ([0, 21, 1.5] as $invalidCount) {
            $this->postJson('/rsvp', ['nama' => '   ', 'jumlah_hadir' => $invalidCount, 'status' => 'unknown',
                'pesan' => str_repeat('x', 1001)])->assertJsonValidationErrors(['nama', 'jumlah_hadir', 'status', 'pesan']);
        }
        $this->postJson('/rsvp', ['nama' => 'Rani', 'jumlah_hadir' => 1, 'status' => 'hadir'])
            ->assertJsonValidationErrors(['form']);
        $this->assertDatabaseCount('tamus', 0);
    }

    public function test_only_approved_messages_are_public_escaped_and_paginated(): void
    {
        $event = $this->createEvent();
        $guest = Tamu::create(['nama' => 'Rani', 'kode_undangan' => (string) Str::uuid()]);
        foreach (range(1,11) as $i) {
            Ucapan::create(['acara_id' => $event->id, 'tamu_id' => $guest->id, 'pesan' => "Ucapan nomor {$i}.", 'is_approved' => true]);
        }
        Ucapan::create(['acara_id' => $event->id, 'tamu_id' => $guest->id, 'pesan' => '<script>contoh()</script>', 'is_approved' => true]);
        Ucapan::create(['acara_id' => $event->id, 'tamu_id' => $guest->id, 'pesan' => 'Pesan tertunda', 'is_approved' => false]);
        $this->withoutVite()->get('/')->assertOk()->assertSee('<script>contoh()</script>')
            ->assertDontSee('<script>contoh()</script>', false)->assertDontSee('Ucapan nomor 1.')
            ->assertDontSee('Pesan tertunda')->assertSee('Berikutnya');
        $this->get('/?buku=2')->assertOk()->assertSee('Ucapan nomor 1.');
    }

    public function test_guestbook_includes_approved_legacy_and_reception_messages_for_this_invitation(): void
    {
        $event = $this->createEvent();
        $reception = $event->replicate();
        $reception->nama_acara = 'Resepsi';
        $reception->save();
        $other = $event->replicate();
        $other->mempelai_id = Mempelai::create(['nama_pria' => 'Lain', 'nama_wanita' => 'Lain'])->id;
        $other->save();
        $guest = Tamu::create(['nama' => 'Rani', 'kode_undangan' => (string) Str::uuid()]);

        foreach ([
            [null, true, 'Ucapan lama disetujui'],
            [null, false, 'Ucapan lama tertunda'],
            [$reception->id, true, 'Ucapan resepsi disetujui'],
            [$other->id, true, 'Ucapan pasangan lain'],
        ] as [$eventId, $approved, $message]) {
            Ucapan::create(['acara_id' => $eventId, 'tamu_id' => $guest->id, 'pesan' => $message, 'is_approved' => $approved]);
        }

        $this->withoutVite()->get('/')->assertOk()
            ->assertSee('Ucapan lama disetujui')->assertSee('Ucapan resepsi disetujui')
            ->assertDontSee('Ucapan lama tertunda')->assertDontSee('Ucapan pasangan lain');
    }

    public function test_public_submissions_are_rate_limited(): void
    {
        foreach (range(1,5) as $i) {
            $this->postJson('/rsvp', [])->assertUnprocessable();
        }
        $this->postJson('/rsvp', [])->assertTooManyRequests();
        $this->postJson('/ucapan', [])->assertTooManyRequests();
    }
}
