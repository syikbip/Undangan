<?php

namespace App\Filament\Resources\Acaras\Pages;

use App\Filament\Resources\Acaras\AcaraResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAcara extends EditRecord
{
    protected static string $resource = AcaraResource::class;

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()->label('Simpan Perubahan');
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()->label('Hapus')->outlined(),
        ];
    }
}
