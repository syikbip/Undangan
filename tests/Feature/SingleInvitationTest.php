<?php

namespace Tests\Feature;

use App\Filament\Resources\Acaras\Pages\CreateAcara;
use App\Filament\Resources\Galeris\Pages\EditGaleri;
use App\Models\{Galeri, Mempelai, Rsvp, Tamu, UndanganAktif, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SingleInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_retired_event_and_response_are_removed_without_deleting_guest_or_main_data(): void
    {
        $main = UndanganAktif::acaraAktif();
        $legacy = Mempelai::create(['nama_pria' => 'Budi', 'nama_wanita' => 'Siti']);
        $event = $legacy->acaras()->create(['nama_acara' => 'Akad', 'tanggal' => '2027-09-20']);
        $guest = Tamu::create(['nama' => 'Budi Santoso', 'kode_undangan' => 'TEST']);
        $response = Rsvp::create(['tamu_id' => $guest->id, 'acara_id' => $event->id, 'status' => 'hadir', 'jumlah_hadir' => 1]);
        UndanganAktif::pilih($event);
        $migration = require database_path('migrations/2026_11_24_020000_retire_legacy_invitation.php');
        $migration->up();
        $migration->up();
        $this->assertModelMissing($legacy);
        $this->assertModelMissing($response);
        $this->assertModelMissing($event);
        $this->assertModelExists($guest);
        $this->assertTrue($main->is(UndanganAktif::acaraAktif()));
    }

    public function test_admin_pages_are_limited_to_the_main_invitation(): void
    {
        $this->withoutVite();
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
        $this->actingAs(User::factory()->create());
        $main = UndanganAktif::acaraAktif();
        $other = Mempelai::create(['nama_pria' => 'Lain', 'nama_wanita' => 'Lain']);
        $foreign = $other->acaras()->create(['nama_acara' => 'Acara luar', 'tanggal' => '2027-09-20']);
        $otherPhoto = $other->galeris()->create(['judul' => 'Foto luar', 'foto' => 'test.webp']);
        foreach (['acaras', 'galeris', 'tamus', 'rsvps', 'ucapans'] as $page) {
            $this->get('/admin/'.$page)->assertOk()->assertDontSee('Acara luar')->assertDontSee('Foto luar');
        }
        $this->get('/admin/mempelais')->assertRedirect('/admin/mempelais/'.$main->mempelai_id.'/edit');
        $this->get('/admin/mempelais/create')->assertNotFound();
        $this->get('/admin/mempelais/'.$other->id.'/edit')->assertNotFound();
        $this->get('/admin/acaras/'.$foreign->id.'/edit')->assertNotFound();
        $this->get('/admin/galeris/'.$otherPhoto->id.'/edit')->assertNotFound();

        Livewire::test(CreateAcara::class)->fillForm(['nama_acara' => 'Resepsi', 'tanggal' => '2026-12-20'])
            ->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('acaras', ['nama_acara' => 'Resepsi', 'mempelai_id' => $main->mempelai_id]);
        \Illuminate\Support\Facades\Storage::fake('public')->put('test.webp', file_get_contents(public_path('assets/wedding/couple-gold-branch.webp')));
        $photo = Galeri::create(['mempelai_id' => $main->mempelai_id, 'judul' => 'Awal', 'foto' => 'test.webp']);
        Livewire::test(EditGaleri::class, ['record' => $photo->id])->fillForm(['judul' => 'Album baru'])
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame('Album baru', $photo->refresh()->judul);
    }
}

