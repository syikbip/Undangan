<?php

namespace Tests\Feature;

use App\Filament\Resources\Mempelais\Pages\EditMempelai;
use App\Models\UndanganAktif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class WeddingMusicTest extends TestCase
{
    use RefreshDatabase;

    public function test_music_follows_saved_settings_and_file_availability(): void
    {
        $disk = Storage::fake('public');
        $this->withoutVite();
        $this->actingAs(User::factory()->create());
        $couple = UndanganAktif::acaraAktif()->mempelai;
        $this->get('/')->assertDontSee('id="invitationMusic"', false);

        $form = Livewire::test(EditMempelai::class, ['record' => $couple->id]);
        $form->fillForm(['music_file' => UploadedFile::fake()->create('bukan-audio.pdf', 100, 'application/pdf')])
            ->call('save')->assertHasFormErrors(['music_file']);
        $form->fillForm(['music_file' => [], 'music_enabled' => true])->call('save')->assertHasFormErrors(['music_file' => 'required']);
        $form->fillForm(['music_file' => UploadedFile::fake()->create('lagu.mp3', 100, 'audio/mpeg'),
            'music_volume' => 42, 'music_autoplay_on_open' => false])->call('save')->assertHasNoFormErrors();
        $first = $couple->refresh()->music_file;
        $disk->assertExists($first);
        $this->assertSame('lagu.mp3', $couple->music_filename);
        $this->get('/')->assertSee($first)->assertSee('data-volume="42"', false)->assertSee('data-start-on-open="0"', false);

        $form = Livewire::test(EditMempelai::class, ['record' => $couple->id]);
        $form->fillForm(['music_file' => []]);
        $form->fillForm(['music_file' => UploadedFile::fake()->create('baru.mp3', 100, 'audio/mpeg')])
            ->call('save')->assertHasNoFormErrors();
        $second = $couple->refresh()->music_file;
        $this->assertNotSame($first, $second);
        $disk->assertMissing($first);
        $disk->assertExists($second);
        $this->get('/')->assertSee($second)->assertDontSee($first);

        $form->fillForm(['music_enabled' => false])->call('save')->assertHasNoFormErrors();
        $this->get('/')->assertDontSee('id="musicControl"', false);
        $couple->update(['music_enabled' => true]);
        $disk->delete($second);
        $this->get('/')->assertDontSee('id="musicControl"', false);
        $form->fillForm(['music_enabled' => false, 'music_file' => []])->call('save')->assertHasNoFormErrors();
        $this->assertNull($couple->refresh()->music_file);
        $this->assertNull($couple->music_filename);
    }
}
