<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->foreignId('segmento_id')
                ->nullable()
                ->after('segmento')
                ->constrained('segmentos')
                ->nullOnDelete();
        });

        $segmentos = DB::table('segmentos')->pluck('id', 'name');

        $empresas = DB::table('empresas')->whereNotNull('segmento')->get(['id', 'segmento']);
        foreach ($empresas as $empresa) {
            if (isset($segmentos[$empresa->segmento])) {
                DB::table('empresas')
                    ->where('id', $empresa->id)
                    ->update(['segmento_id' => $segmentos[$empresa->segmento]]);
            }
        }

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('segmento');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('segmento')->nullable()->after('empestado');
        });

        $segmentos = DB::table('segmentos')->pluck('name', 'id');

        $empresas = DB::table('empresas')->whereNotNull('segmento_id')->get(['id', 'segmento_id']);
        foreach ($empresas as $empresa) {
            if (isset($segmentos[$empresa->segmento_id])) {
                DB::table('empresas')
                    ->where('id', $empresa->id)
                    ->update(['segmento' => $segmentos[$empresa->segmento_id]]);
            }
        }

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('segmento_id');
        });
    }
};
