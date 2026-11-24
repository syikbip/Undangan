<?php

namespace App\Filament\Resources\Acaras\Pages;

use App\Filament\Resources\Acaras\AcaraResource;
use App\Models\UndanganAktif;
use Filament\Resources\Pages\CreateRecord;

class CreateAcara extends CreateRecord
{
    protected static string $resource = AcaraResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['mempelai_id'] = UndanganAktif::queryIdMempelaiUtama()->value('mempelai_id');
        abort_if(! $data['mempelai_id'], 404);

        return $data;
    }
}
