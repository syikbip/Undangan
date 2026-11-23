<?php

namespace App\Filament\Resources\Mempelais\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use App\Models\Mempelai;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MempelaiForm
{
    private static function teksFields(): array
    {
        $labels = [
            'pembuka' => 'Pesan pembuka',
            'pengantar_mempelai' => 'Pengantar mempelai',
            'kutipan' => 'Kutipan Kisah Cinta',
            'pengantar_galeri' => 'Pengantar galeri',
            'pengantar_acara' => 'Pengantar acara',
            'pengantar_rsvp' => 'Pengantar RSVP',
            'penutup' => 'Ucapan terima kasih di penutup',
        ];

        $fields = [];
        foreach ($labels as $key => $label) {
            $field = Textarea::make('teks_undangan.'.$key)->label($label)
                ->default(Mempelai::TEKS_BAWAAN[$key])->maxLength(2000)->rows(2);
            if ($key === 'pengantar_acara') {
                $field->helperText('Gunakan {acara} untuk menyisipkan nama acara aktif.');
            }
            $fields[] = $field;
        }

        return $fields;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_pria')
                    ->label('Nama Mempelai Pria')
                    ->required(),

                TextInput::make('nama_wanita')
                    ->label('Nama Mempelai Wanita')
                    ->required(),

                FileUpload::make('foto_pria')
                    ->label('Foto mempelai pria')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->image()
                    ->disk('public')
                    ->directory('mempelai')
                    ->imagePreviewHeight('200')->imageEditor(),

                FileUpload::make('foto_wanita')
                    ->label('Foto mempelai wanita')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->image()
                    ->disk('public')
                    ->directory('mempelai')
                    ->imagePreviewHeight('200')->imageEditor(),

                FileUpload::make('story_photo')->label('Foto Kisah Cinta')->image()->disk('public')->directory('mempelai'),
                Textarea::make('deskripsi')
                    ->label('Kisah Cinta')
                    ->columnSpanFull(),
                Section::make('Teks undangan')
                    ->description('Atur kalimat untuk pasangan ini. Kosongkan kolom untuk menyembunyikan teks tersebut.')
                    ->columnSpanFull()
                    ->schema(static::teksFields()),
            ]);
    }
}
