<?php

namespace App\Policies;

use App\Models\Mempelai;
use App\Models\UndanganAktif;
use App\Models\User;

class MempelaiPolicy
{
    public function delete(User $user, Mempelai $record): bool
    {
        return $record->acaras()->whereIn('id', UndanganAktif::select('acara_id'))->doesntExist();
    }
}
