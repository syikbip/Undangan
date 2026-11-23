<?php

namespace Database\Seeders;

use App\Models\Acara;
use App\Models\Galeri;
use App\Models\Mempelai;
use App\Models\UndanganAktif;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UndanganIdhamKetrinSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            if (UndanganAktif::acaraAktif(true)) {
                return;
            }

            $couple = Mempelai::firstOrCreate(['nama_pria' => 'Idham', 'nama_wanita' => 'Ketrin'], [
                'deskripsi' => "Kisah Idham dan Ketrin berawal dari pertemuan sederhana. Seiring waktu, percakapan mereka tumbuh semakin akrab. Ada cerita kecil yang ingin segera dibagikan, juga hari-hari ketika duduk bersama sudah terasa cukup.\n\nDari kebersamaan itu, keduanya belajar mendengarkan dan memahami satu sama lain. Kini, dengan penuh syukur, Idham dan Ketrin memilih untuk menikah. Mereka ingin membangun rumah tempat keduanya bisa pulang, bercerita, dan tumbuh bersama.",
                'foto_pria' => 'mempelai/KNzzJjMW7MG62JgBUrpmLVUn1wY4GeRiMiA7aLna.jpg',
                'foto_wanita' => 'mempelai/y7cCYinX0WXgzEDMgHLwNj1Re9RrGdn22DgIZsHc.jpg',
                'story_photo' => 'galeri/idham-ketrin-d3379654f552cde9.webp',
            ]);
            $event = Acara::firstOrCreate([
                'mempelai_id' => $couple->id, 'nama_acara' => 'Akad Nikah', 'tanggal' => '2026-12-20',
            ], ['maps_url' => config('wedding.akad_location_url')]);

            $photos = array_map(fn ($hash) => "galeri/idham-ketrin-{$hash}.webp", [
                'a837f9e723f65e0d', 'c64b0edf06705e6f', 'cd214a61c83e370d', 'd3379654f552cde9',
                '915ffb6f3ea0fe7e', 'fbe92562deec1381', '9373ec4ae24038f1', 'd21904f46268c604',
                'fa3499c0bde48786', '0b8af06eb27ed4f7', '2aceecd2fc0ca9a6', '687eff8feca0db0d',
                '14ec785caaf4670b', '0e51ba343a1f6117', '6c36a4a84b7c22ad', 'b8a3066b6f06345c',
                '38e8412773055486', '6069a5a219bb8847',
            ]);
            $photos[] = 'galeri/2WEBSuRDvkzu5xPlHzUtoDM8QKi7jk8rjdWkPyr3.jpg';
            $photos[] = 'galeri/VqxLsIt89MbzuYKn8SXutilCIYYQ9TyfvQGBRKMM.jpg';
            Galeri::whereIn('foto', $photos)->whereHas('mempelai', fn ($query) =>
                $query->where('nama_pria', 'Budi')->where('nama_wanita', 'Siti')
            )->update(['mempelai_id' => $couple->id]);
            UndanganAktif::pilih($event);
        });
    }
}
