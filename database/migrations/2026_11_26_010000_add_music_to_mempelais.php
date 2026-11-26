<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mempelais', function (Blueprint $table) {
            $table->boolean('music_enabled')->default(false);
            $table->string('music_file')->nullable();
            $table->string('music_filename')->nullable();
            $table->unsignedTinyInteger('music_volume')->default(35);
            $table->boolean('music_autoplay_on_open')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('mempelais', function (Blueprint $table) {
            $table->dropColumn(['music_enabled', 'music_file', 'music_filename', 'music_volume', 'music_autoplay_on_open']);
        });
    }
};
