<?php

namespace App\Filament\Resources\Galeris\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class GaleriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->label('Judul Foto')
                    ->placeholder('Contoh: Foto Prewedding 1'),

                FileUpload::make('foto')
                    ->label('Foto galeri')->columnSpanFull()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->image()
                    ->disk('public')
                    ->directory('galeri')
                    ->imagePreviewHeight('220')->imageEditor()
                    ->required(),
                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}