<?php

use Database\Seeders\UndanganIdhamKetrinSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        (new UndanganIdhamKetrinSeeder)->prepare(upgrade: true);

        Schema::table('undangan_aktif', function (Blueprint $table) {
            $table->dropForeign(['acara_id']);
            $table->foreign('acara_id')->references('id')->on('acaras')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('undangan_aktif', function (Blueprint $table) {
            $table->dropForeign(['acara_id']);
            $table->foreign('acara_id')->references('id')->on('acaras')->nullOnDelete();
        });
    }
};
