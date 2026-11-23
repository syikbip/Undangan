<?php

namespace Tests\Feature;

use App\Filament\Resources\Mempelais\Pages\EditMempelai;
use App\Models\Acara;
use App\Models\Mempelai;
use App\Models\UndanganAktif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeksUndanganTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_customize_or_hide_text_for_one_couple(): void
    {
        $this->withoutVite()->actingAs(User::factory()->create());
        $first = Mempelai::create(['nama_pria' => 'Idham', 'nama_wanita' => 'Ketrin']);
        $second = Mempelai::create(['nama_pria' => 'Arif', 'nama_wanita' => 'Maya']);
        $event = Acara::create(['mempelai_id' => $first->id, 'nama_acara' => 'Resepsi', 'tanggal' => '2026-12-20']);
        UndanganAktif::pilih($event);
        $this->get('/')->assertSee('Terima kasih atas doa dan kasih sayang');

        $texts = [
            'pembuka' => 'Pembuka pilihan kami',
            'pengantar_mempelai' => 'Sepatah kata dari kami',
            'kutipan' => '',
            'pengantar_galeri' => 'Potret bersama',
            'pengantar_acara' => 'Mari hadiri {acara}.',
            'pengantar_rsvp' => 'Silakan beri kabar.',
            'penutup' => 'Salam <hangat> dari kami',
        ];
        Livewire::test(EditMempelai::class, ['record' => $first->getRouteKey()])
            ->assertFormSet(['teks_undangan.penutup' => Mempelai::TEKS_BAWAAN['penutup']])
            ->fillForm(['teks_undangan' => $texts])->call('save')->assertHasNoFormErrors();
        $this->get('/')->assertSee('Pembuka pilihan kami')->assertSee('Sepatah kata dari kami')
            ->assertSee('Potret bersama')->assertSee('Mari hadiri resepsi.')->assertSee('Silakan beri kabar.')
            ->assertSee('Salam <hangat> dari kami')->assertDontSee('<blockquote>', false);
        UndanganAktif::pilih(Acara::create(['mempelai_id' => $second->id, 'nama_acara' => 'Akad', 'tanggal' => '2027-01-05']));
        $this->get('/')->assertSee('Terima kasih atas doa dan kasih sayang')->assertDontSee('Potret bersama');
    }
}
