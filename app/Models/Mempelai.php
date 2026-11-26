<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Mempelai extends Model
{
    protected $fillable = [
        'nama_pria',
        'nama_wanita',
        'foto_pria',
        'foto_wanita',
        'deskripsi',
        'story_photo',
        'teks_undangan',
        'tanda_kasih',
        'music_enabled', 'music_file', 'music_filename', 'music_volume', 'music_autoplay_on_open',
    ];

    protected $casts = ['teks_undangan' => 'array', 'tanda_kasih' => 'array'];

    protected static function booted(): void
    {
        static::saving(function (self $couple) {
            if ($couple->isDirty('music_file') && ! $couple->music_file) {
                $couple->music_filename = null;
                $couple->music_enabled = false;
            }
        });

        static::updated(function (self $couple) {
            if ($couple->wasChanged('music_file')) {
                $couple->removeUnusedMusic($couple->getRawOriginal('music_file'));
            }
        });
        static::deleted(fn (self $couple) => $couple->removeUnusedMusic($couple->music_file));
    }

    private function removeUnusedMusic(?string $file): void
    {
        if ($file && str_starts_with($file, 'musik/')) {
            $this->getConnection()->afterCommit(function () use ($file) {
                if (! static::where('music_file', $file)->exists()) {
                    Storage::disk('public')->delete($file);
                }
            });
        }
    }

    public const TEKS_BAWAAN = [
        'pembuka' => "Dengan penuh syukur, kami mengundang\nAnda untuk hadir di hari bahagia kami.",
        'pengantar_mempelai' => "Sebentar lagi, kami akan memulai keseharian baru\nsebagai suami dan istri. Doakan agar kami selalu punya\nwaktu untuk saling mendengar, tertawa bersama,\ndan menjaga satu sama lain.",
        'kutipan' => '"May we always find a little time to sit together and tell each other about our day."',
        'pengantar_galeri' => 'Beberapa momen yang ingin kami simpan dan bagikan.',
        'pengantar_acara' => 'Dengan penuh syukur, kami mengundang Anda untuk menyaksikan {acara} kami.',
        'pengantar_rsvp' => 'Kabari kami apakah Anda bisa hadir di hari bahagia kami.',
        'penutup' => "Terima kasih atas doa dan kasih sayang\nyang mengiringi langkah kami.",
    ];

    public function teksUndangan(): array
    {
        return array_replace(self::TEKS_BAWAAN, $this->teks_undangan ?? []);
    }

    public function acaras()
    {
        return $this->hasMany(Acara::class);
    }

    public function galeris()
    {
        return $this->hasMany(Galeri::class);
    }
}
