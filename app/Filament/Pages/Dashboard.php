<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as FilamentDashboard;

class Dashboard extends FilamentDashboard
{
    protected static ?string $title = 'Dasbor';

    public function getColumns(): int|array
    {
        return 1;
    }
}
