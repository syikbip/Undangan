<?php

namespace App\Filament\Resources\Galeris\Pages;

use App\Filament\Resources\Galeris\GaleriResource;
use App\Models\UndanganAktif;
use Filament\Resources\Pages\CreateRecord;

class CreateGaleri extends CreateRecord
{
    protected static string $resource = GaleriResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['mempelai_id'] = UndanganAktif::queryIdMempelaiUtama()->value('mempelai_id');
        abort_if(! $data['mempelai_id'], 404);

        return $data;
    }
}
