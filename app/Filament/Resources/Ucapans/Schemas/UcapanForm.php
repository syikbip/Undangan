<?php

namespace App\Filament\Resources\Ucapans\Schemas;

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
                \Filament\Forms\Components\Select::make('acara_id')->label('Undangan')
                    ->placeholder('Belum ditetapkan')
                    ->options(fn () => \App\Models\Acara::with('mempelai')->get()->mapWithKeys(
                        fn ($event) => [$event->id => $event->mempelai->nama_pria.' & '.$event->mempelai->nama_wanita.' / '.$event->nama_acara.' / '.$event->tanggal]
                    ))->searchable(),
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
