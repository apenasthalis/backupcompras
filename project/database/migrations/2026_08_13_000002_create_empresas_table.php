<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('empconta')->nullable()->unique();
            $table->string('empnome')->nullable();
            $table->string('empemail')->nullable();
            $table->string('emptelefone')->nullable();
            $table->string('empchave')->nullable();
            $table->string('empsenha_hash')->nullable();
            $table->enum('empstatus', ['pendente', 'ativo', 'bloqueado'])->default('pendente');
            $table->integer('emptentativas_falhas')->nullable();
            $table->timestamp('empbloqueado_ate')->nullable();
            $table->string('emptoken_recuperacao')->nullable();
            $table->timestamp('emptoken_expira_em')->nullable();
            $table->timestamp('empcriado_em')->nullable();
            $table->timestamp('empatualizado_em')->nullable();
            $table->string('empcontad')->nullable()->unique();
            $table->string('empendereco')->nullable();
            $table->string('empcidade')->nullable();
            $table->string('empestado')->nullable();

            $table->index('empcontad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
