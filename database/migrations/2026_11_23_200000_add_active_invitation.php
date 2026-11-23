<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mempelais', fn (Blueprint $table) => $table->string('story_photo')->nullable());
        Schema::table('acaras', function (Blueprint $table) {
            $table->time('waktu_mulai')->nullable()->change();
            $table->string('lokasi')->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->string('maps_url', 2048)->nullable();
        });
        Schema::table('ucapans', function (Blueprint $table) {
            $table->foreignId('acara_id')->nullable()->constrained('acaras')->nullOnDelete();
        });
        Schema::create('undangan_aktif', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->foreignId('acara_id')->nullable()->constrained('acaras')->nullOnDelete();
        });
        DB::table('undangan_aktif')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::table('mempelais', fn (Blueprint $table) => $table->dropColumn('story_photo'));
        Schema::dropIfExists('undangan_aktif');
        Schema::table('ucapans', fn (Blueprint $table) => $table->dropConstrainedForeignId('acara_id'));
        Schema::table('acaras', fn (Blueprint $table) => $table->dropColumn('maps_url'));
    }
};
