<?php

namespace Tests\Feature;

use App\Filament\Pages\PengaturanUndangan;
use App\Models\Acara;
use App\Models\Galeri;
use App\Models\Mempelai;
use App\Models\Tamu;
use App\Models\Ucapan;
use App\Models\UndanganAktif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class UndanganAktifTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_selection_drives_calendar_pages_and_messages(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());
        $this->get('/admin/undangan-aktif')->assertOk();
        $first = Mempelai::create(['nama_pria' => 'Idham', 'nama_wanita' => 'Ketrin']);
        $second = Mempelai::create(['nama_pria' => 'Arif', 'nama_wanita' => 'Maya', 'deskripsi' => 'Kisah Arif']);
        $a = Acara::create(['mempelai_id' => $first->id, 'nama_acara' => 'Akad Nikah', 'tanggal' => '2026-12-20']);
        $b = Acara::create(['mempelai_id' => $second->id, 'nama_acara' => 'Resepsi', 'tanggal' => '2027-01-02', 'waktu_mulai' => '10:00']);
        Galeri::create(['mempelai_id' => $second->id, 'foto' => 'galeri/maya.webp']);
        $guest = Tamu::create(['nama' => 'Tamu', 'kode_undangan' => (string) Str::uuid()]);
        Ucapan::create(['acara_id' => $a->id, 'tamu_id' => $guest->id, 'pesan' => 'Pesan undangan pertama', 'is_approved' => true]);
        UndanganAktif::pilih($a);
        $this->get('/')->assertSee('20 Desember 2026')->assertSee('Pesan undangan pertama')->assertDontSee('galeri/maya.webp');
        $this->get('/kalender.ics')->assertSee('DTSTART;VALUE=DATE:20261220');
        Livewire::test(PengaturanUndangan::class)->set('pasangan', $second->id)->set('acara', $a->id)
            ->call('simpan')->assertHasErrors('acara')->set('acara', $b->id)->call('simpan')->assertHasNoErrors();
        $b->update(['tanggal' => '2027-01-03']);
        $this->get('/')->assertSee('Minggu, 03 Januari 2027')->assertSee('2027-01-03T10:00:00+07:00')
            ->assertSee('Arif')->assertSee('Kisah Arif')->assertSee('galeri/maya.webp')->assertDontSee('Pesan undangan pertama')->assertDontSee('Ketrin');
        $this->get('/kalender.ics')->assertSee('DTSTART:20270103T030000Z')->assertSee('Resepsi - Arif & Maya', false);
        $data = ['nama' => 'Baru', 'status' => 'hadir', 'jumlah_hadir' => 2, 'pesan' => 'Selamat'];
        $this->postJson('/rsvp', $data + ['acara_id' => $a->id])->assertJsonValidationErrors('form');
        $this->assertDatabaseCount('rsvps', 0);
        $this->postJson('/rsvp', $data + ['acara_id' => $b->id])->assertOk();
        $this->assertDatabaseHas('rsvps', ['acara_id' => $b->id]);
        $this->assertDatabaseHas('ucapans', ['acara_id' => $b->id, 'pesan' => 'Selamat', 'is_approved' => false]);
    }
}
