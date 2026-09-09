<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segmentos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        if (Schema::hasTable('empresas') && Schema::hasColumn('empresas', 'segmento')) {
            $nomes = DB::table('empresas')
                ->whereNotNull('segmento')
                ->distinct()
                ->orderBy('segmento')
                ->pluck('segmento');

            foreach ($nomes as $nome) {
                DB::table('segmentos')->insert([
                    'name' => $nome,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('segmentos');
    }
};
