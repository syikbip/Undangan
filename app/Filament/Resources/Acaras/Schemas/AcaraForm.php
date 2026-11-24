<?php

namespace App\Filament\Resources\Acaras\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AcaraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas acara')->columnSpanFull()->columns(2)->schema([
                    TextInput::make('nama_acara')
                        ->required(),
                ]),
                Section::make('Jadwal')->columnSpanFull()->columns(2)->schema([
                    DatePicker::make('tanggal')->columnSpanFull()
                        ->required(),
                    TimePicker::make('waktu_mulai'),
                    TimePicker::make('waktu_selesai'),
                ]),
                Section::make('Lokasi')->columnSpanFull()->columns(2)->schema([
                    TextInput::make('lokasi'),
                    TextInput::make('maps_url')->label('Tautan Google Maps')->url()->rules(['nullable', 'url:http,https'])->maxLength(2048),
                    Textarea::make('alamat')
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
