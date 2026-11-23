<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UndanganAktif extends Model
{
    protected $table = 'undangan_aktif';
    public $timestamps = false;

    public static function acaraAktif(bool $lock = false): ?Acara
    {
        $setting = static::query()->when($lock, fn ($query) => $query->lockForUpdate())->find(1);

        return $setting?->acara_id ? Acara::with('mempelai')->find($setting->acara_id) : null;
    }

    public static function untukKiriman(mixed $expected, bool $lock = false): Acara
    {
        $event = static::acaraAktif($lock);
        if (! $event || ! is_scalar($expected) || (string) $event->id !== (string) $expected) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form' => 'Undangan telah berubah atau belum tersedia. Muat ulang halaman sebelum mengirim.',
            ]);
        }

        return $event;
    }

    public static function pilih(Acara $acara): void
    {
        static::whereKey(1)->update(['acara_id' => $acara->getKey()]);
    }
}
