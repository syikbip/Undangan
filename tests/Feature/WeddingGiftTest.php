<?php

namespace Tests\Feature;

use App\Filament\Resources\Mempelais\Pages\EditMempelai;
use App\Models\{Acara, Mempelai, UndanganAktif, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WeddingGiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_changes_control_public_transfer_methods(): void
    {
        $this->withoutVite()->actingAs(User::factory()->create());
        $couple = Mempelai::create(['nama_pria' => 'A', 'nama_wanita' => 'B']);
        UndanganAktif::pilih(Acara::create(['mempelai_id' => $couple->id, 'nama_acara' => 'Akad', 'tanggal' => '2026-12-20']));
        $this->get('/')->assertDontSee('id="gift-title"', false);
        $settings = [
            'bank' => ['active' => true, 'name' => 'Bank Uji', 'recipient' => 'Uji A', 'number' => '000314159'],
            'dana' => ['active' => true, 'recipient' => 'Uji B', 'number' => '080001234567'],
        ];
        $edit = Livewire::test(EditMempelai::class, ['record' => $couple->getRouteKey()]);
        $edit->fillForm(['tanda_kasih' => $settings])->call('save')->assertHasNoFormErrors();
        $this->assertSame('000314159', $couple->refresh()->tanda_kasih['bank']['number']);
        $this->get('/')->assertSee('Bank Uji')->assertSee('•••• • 4159')->assertSee('08•• •••• 4567');
        $settings['bank']['active'] = false;
        $edit->fillForm(['tanda_kasih' => $settings])->call('save')->assertHasNoFormErrors();
        $this->get('/')->assertDontSee('000314159')->assertSee('080001234567');
    }
}
