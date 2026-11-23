<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mempelais', fn (Blueprint $table) => $table->json('teks_undangan')->nullable());
    }

    public function down(): void
    {
        Schema::table('mempelais', fn (Blueprint $table) => $table->dropColumn('teks_undangan'));
    }
};
