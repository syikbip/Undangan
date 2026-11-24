<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mempelais', function (Blueprint $table) {
            $table->json('tanda_kasih')->nullable();
        });

        // Carry forward transfer settings for the invitation that previously displayed them.
        $couple = DB::table('undangan_aktif')->join('acaras', 'acaras.id', '=', 'undangan_aktif.acara_id')
            ->where('undangan_aktif.id', 1)->value('acaras.mempelai_id');
        if ($couple) {
            $settings = [];
            foreach (['bank', 'dana'] as $method) {
                $entry = [
                    'number' => (string) config("wedding.{$method}_number"),
                    'recipient' => config("wedding.{$method}_recipient"),
                ];
                if ($method === 'bank') {
                    $entry['name'] = config('wedding.bank_name');
                }
                $entry['active'] = ! in_array(null, $entry, true) && ! in_array('', $entry, true)
                    && preg_match('/^[0-9]{5,}$/D', $entry['number']) === 1;
                $settings[$method] = $entry;
            }
            DB::table('mempelais')->where('id', $couple)->update(['tanda_kasih' => json_encode($settings, JSON_THROW_ON_ERROR)]);
        }
    }

    public function down(): void
    {
        Schema::table('mempelais', fn (Blueprint $table) => $table->dropColumn('tanda_kasih'));
    }
};
