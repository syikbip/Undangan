<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Ucapan extends Model
{
    protected $fillable = [
        'tamu_id',
        'acara_id',
        'pesan',
        'is_approved',
    ];

    public function scopeUntukBukuTamu(Builder $query): Builder
    {
        $acaraUndangan = Acara::query()->select('id')
            ->whereIn('mempelai_id', UndanganAktif::queryIdMempelaiUtama());

        return $query->where(fn (Builder $pesan) => $pesan
            ->whereNull('acara_id')
            ->orWhereIn('acara_id', $acaraUndangan));
    }

    public function tamu()
    {
        return $this->belongsTo(Tamu::class);
    }
}
