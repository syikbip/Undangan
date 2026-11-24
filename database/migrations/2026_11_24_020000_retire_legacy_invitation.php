<?php

use App\Models\UndanganAktif;
use Database\Seeders\UndanganIdhamKetrinSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $legacy = DB::table('mempelais')->where('nama_pria', 'Budi')->where('nama_wanita', 'Siti')
                ->lockForUpdate()->pluck('id');
            if ($legacy->isEmpty()) {
                return;
            }

            $main = UndanganAktif::acaraAktif(true);
            if (! $main || $legacy->contains($main->mempelai_id)) {
                (new UndanganIdhamKetrinSeeder)->prepare(upgrade: true);
            }
            $events = DB::table('acaras')->whereIn('mempelai_id', $legacy)->select('id');
            DB::table('ucapans')->whereIn('acara_id', $events)->delete();
            // Foreign keys remove the retired events and their RSVPs, preserving guest identities.
            DB::table('mempelais')->whereIn('id', $legacy)->delete();
        });
    }

    public function down(): void
    {
        // Retired invitation data cannot be reconstructed.
    }
};
