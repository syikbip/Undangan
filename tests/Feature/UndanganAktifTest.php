<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Galeri;
use App\Models\Rsvp;
use Database\Seeders\UndanganIdhamKetrinSeeder;
use Illuminate\Database\QueryException;
use App\Models\Mempelai;
use App\Models\Tamu;
use App\Models\Ucapan;
use App\Models\UndanganAktif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UndanganAktifTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_event_drives_calendar_pages_and_messages(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());
        $this->get('/admin/undangan-aktif')->assertNotFound();
        $a = UndanganAktif::acaraAktif();
        $this->assertSame('Idham', $a->mempelai->nama_pria);
        $this->assertSame('Ketrin', $a->mempelai->nama_wanita);
        $second = Mempelai::create(['nama_pria' => 'Arif', 'nama_wanita' => 'Maya', 'deskripsi' => 'Kisah Arif']);
        $b = Acara::create(['mempelai_id' => $second->id, 'nama_acara' => 'Resepsi', 'tanggal' => '2027-01-02', 'waktu_mulai' => '10:00']);
        Galeri::create(['mempelai_id' => $second->id, 'foto' => 'galeri/maya.webp']);
        $guest = Tamu::create(['nama' => 'Tamu', 'kode_undangan' => (string) Str::uuid()]);
        Ucapan::create(['acara_id' => $a->id, 'tamu_id' => $guest->id, 'pesan' => 'Pesan undangan pertama', 'is_approved' => true]);
        $this->get('/')->assertSee('20 Desember 2026')->assertSee('Pesan undangan pertama')->assertDontSee('galeri/maya.webp');
        $this->get('/kalender.ics')->assertSee('DTSTART:20261220T030000Z');
        $a->update(['tanggal' => '2027-01-03']);
        $this->get('/')->assertSee('Minggu, 03 Januari 2027')->assertSee('Pesan undangan pertama')->assertDontSee('galeri/maya.webp');
        $this->get('/kalender.ics')->assertSee('DTSTART:20270103T030000Z');
        $data = ['nama' => 'Baru', 'status' => 'hadir', 'jumlah_hadir' => 2, 'pesan' => 'Selamat'];
        $this->postJson('/rsvp', $data + ['acara_id' => $b->id])->assertJsonValidationErrors('form');
        $this->assertDatabaseCount('rsvps', 0);
        $this->postJson('/rsvp', $data + ['acara_id' => $a->id])->assertOk();
        $this->assertDatabaseHas('rsvps', ['acara_id' => $a->id]);
        $this->assertDatabaseHas('ucapans', ['acara_id' => $a->id, 'pesan' => 'Selamat', 'is_approved' => false]);
    }

    public function test_upgrade_reuses_existing_akad_and_preserves_admin_values_and_responses(): void
    {
        $akad = UndanganAktif::acaraAktif();
        $akad->update(['tanggal' => '2026-12-22', 'waktu_mulai' => '11:00:00', 'maps_url' => 'https://example.test/venue']);
        $guest = Tamu::create(['nama' => 'Tamu', 'kode_undangan' => (string) Str::uuid()]);
        $response = Rsvp::create(['acara_id' => $akad->id, 'tamu_id' => $guest->id, 'status' => 'hadir', 'jumlah_hadir' => 2]);
        $another = Mempelai::create(['nama_pria' => 'Arif', 'nama_wanita' => 'Maya']);
        $otherEvent = $another->acaras()->create(['nama_acara' => 'Akad Nikah', 'tanggal' => '2027-01-02']);
        UndanganAktif::pilih($otherEvent);

        (new UndanganIdhamKetrinSeeder)->prepare(upgrade: true);
        $akad->mempelai->update(['nama_pria' => 'Idham Putra']);
        $this->seed(UndanganIdhamKetrinSeeder::class);

        $this->assertTrue($akad->is(UndanganAktif::acaraAktif()));
        $this->assertDatabaseCount('acaras', 2);
        $this->assertDatabaseCount('mempelais', 2);
        $this->assertDatabaseHas('acaras', ['id' => $akad->id, 'tanggal' => '2026-12-22', 'waktu_mulai' => '11:00:00', 'maps_url' => 'https://example.test/venue']);
        $this->assertDatabaseHas('rsvps', ['id' => $response->id, 'acara_id' => $akad->id]);
    }

    public function test_main_records_are_protected_from_deletion(): void
    {
        $admin = User::factory()->create();
        $akad = UndanganAktif::acaraAktif();
        foreach ([$akad, $akad->mempelai] as $record) {
            $this->assertFalse($admin->can('delete', $record));
            try {
                $record->delete();
                $this->fail('Penghapusan data utama harus ditolak.');
            } catch (QueryException $exception) {
                $this->assertModelExists($record);
            }
        }
        $this->assertTrue($akad->is(UndanganAktif::acaraAktif()));
    }
}
