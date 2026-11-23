<?php

namespace App\Policies;

use App\Models\Acara;
use App\Models\UndanganAktif;
use App\Models\User;

class AcaraPolicy
{
    public function delete(User $user, Acara $record): bool
    {
        return UndanganAktif::where('acara_id', $record->id)->doesntExist();
    }
}
