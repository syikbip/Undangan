<?php

namespace App\Filament\Resources\Mempelais\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use App\Models\Mempelai;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
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
                Section::make('Musik latar')->columnSpanFull()->schema([
                    Toggle::make('music_enabled')->label('Aktifkan musik latar')->default(false)->live(),
                    FileUpload::make('music_file')->label('Lagu undangan')
                        ->disk('public')->directory('musik')->visibility('public')
                        ->storeFileNamesIn('music_filename')->preventFilePathTampering()
                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/ogg', 'application/ogg'])
                        ->maxSize(10240)->required(fn (Get $get) => (bool) $get('music_enabled'))
                        ->helperText('MP3, M4A, atau OGG, maksimal 10 MB. Gunakan lagu yang Anda miliki izin pemakaiannya.'),
                    TextInput::make('music_volume')->label('Volume awal')->integer()->minValue(0)->maxValue(100)
                        ->suffix('%')->default(35)->required(),
                    Toggle::make('music_autoplay_on_open')->label('Putar saat tamu menekan Buka Undangan')->default(true),
                ]),
                Section::make('Tanda Kasih')->columnSpanFull()->schema([
                    Section::make('Transfer Bank')->schema([
                        Toggle::make('tanda_kasih.bank.active')->label('Aktifkan Transfer Bank')->live(),
                        TextInput::make('tanda_kasih.bank.name')->label('Nama bank')->maxLength(150)
                            ->required(fn (Get $get) => (bool) $get('tanda_kasih.bank.active')),
                        TextInput::make('tanda_kasih.bank.recipient')->label('Nama pemilik rekening')->maxLength(150)
                            ->required(fn (Get $get) => (bool) $get('tanda_kasih.bank.active')),
                        TextInput::make('tanda_kasih.bank.number')->label('Nomor rekening')->inputMode('numeric')
                            ->regex('/^[0-9]{5,64}$/D')->required(fn (Get $get) => (bool) $get('tanda_kasih.bank.active')),
                    ]),
                    Section::make('DANA')->schema([
                        Toggle::make('tanda_kasih.dana.active')->label('Aktifkan DANA')->live(),
                        TextInput::make('tanda_kasih.dana.recipient')->label('Nama penerima DANA')->maxLength(150)
                            ->required(fn (Get $get) => (bool) $get('tanda_kasih.dana.active')),
                        TextInput::make('tanda_kasih.dana.number')->label('Nomor DANA')->inputMode('numeric')
                            ->regex('/^[0-9]{7,64}$/D')->required(fn (Get $get) => (bool) $get('tanda_kasih.dana.active')),
                    ]),
                ]),
                Section::make('Teks undangan')
                    ->description('Atur kalimat untuk pasangan ini. Kosongkan kolom untuk menyembunyikan teks tersebut.')
                    ->columnSpanFull()
                    ->schema(static::teksFields()),
            ]);
    }
}
