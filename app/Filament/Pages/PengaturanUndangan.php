<?php

namespace App\Filament\Pages;

use App\Models\Acara;
use App\Models\Mempelai;
use App\Models\UndanganAktif;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\Rule;

class PengaturanUndangan extends Page
{
    protected static ?string $title = 'Undangan Aktif';
    protected static ?string $slug = 'undangan-aktif';
    protected string $view = 'filament.pages.undangan-aktif';

    public ?int $pasangan = null;
    public ?int $acara = null;

    public function mount(): void
    {
        $aktif = UndanganAktif::acaraAktif();
        $this->pasangan = $aktif?->mempelai_id;
        $this->acara = $aktif?->id;
    }

    public function updatedPasangan(): void
    {
        $this->acara = null;
    }

    public function simpan(): void
    {
        $this->validate([
            'pasangan' => ['required', 'exists:mempelais,id'],
            'acara' => ['required', Rule::exists('acaras', 'id')->where('mempelai_id', $this->pasangan)],
        ]);
        UndanganAktif::pilih(Acara::where('mempelai_id', $this->pasangan)->findOrFail($this->acara));
        Notification::make()->success()->title('Undangan aktif disimpan')->send();
    }

    protected function getViewData(): array
    {
        return [
            'pasangans' => Mempelai::orderBy('nama_pria')->get(),
            'acaras' => Acara::where('mempelai_id', $this->pasangan)->orderBy('tanggal')->get(),
        ];
    }
}
