<?php

namespace App\Filament\Resources\Ucapans\Schemas;

use App\Filament\Resources\Acaras\AcaraResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UcapanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('acara_id')->label('Acara')
                    ->options(fn () => AcaraResource::getEloquentQuery()->orderBy('tanggal')->pluck('nama_acara', 'id'))
                    ->in(fn () => AcaraResource::getEloquentQuery()->pluck('id')->all())->searchable(),
                TextInput::make('tamu_id')
                    ->required()
                    ->numeric(),
                Textarea::make('pesan')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->required(),
            ]);
    }
}
