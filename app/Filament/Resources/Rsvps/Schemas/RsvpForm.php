<?php

namespace App\Filament\Resources\Rsvps\Schemas;

use Filament\Forms\Components\Select;
use App\Filament\Resources\Acaras\AcaraResource;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RsvpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tamu_id')
                    ->required()
                    ->numeric(),
                Select::make('acara_id')->label('Acara')
                    ->options(fn () => AcaraResource::getEloquentQuery()->orderBy('tanggal')->pluck('nama_acara', 'id'))
                    ->in(fn () => AcaraResource::getEloquentQuery()->pluck('id')->all())->searchable()->required(),
                Select::make('status')
                    ->options(['hadir' => 'Hadir', 'tidak_hadir' => 'Tidak hadir'])
                    ->required(),
                TextInput::make('jumlah_hadir')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
