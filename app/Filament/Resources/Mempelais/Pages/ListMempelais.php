<?php

namespace App\Filament\Resources\Mempelais\Pages;

use App\Filament\Resources\Mempelais\MempelaiResource;
use App\Models\UndanganAktif;
use Filament\Resources\Pages\ListRecords;

class ListMempelais extends ListRecords
{
    protected static string $resource = MempelaiResource::class;

    public function mount(): void
    {
        parent::mount();
        $id = UndanganAktif::queryIdMempelaiUtama()->value('mempelai_id');
        abort_if(! $id, 404);
        $this->redirect(MempelaiResource::getUrl('edit', ['record' => $id]));
    }
}
