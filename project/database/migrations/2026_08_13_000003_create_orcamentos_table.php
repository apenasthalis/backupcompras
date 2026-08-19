<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->bigIncrements('idorc');
            $table->string('idempresa')->nullable()->index();
            $table->string('empnome')->nullable();
            $table->string('empendereco')->nullable();
            $table->string('empcidade')->nullable();
            $table->string('empestado')->nullable();
            $table->string('idcliente')->nullable()->index();
            $table->string('clinome')->nullable();
            $table->string('cliendereco')->nullable();
            $table->string('clicidade')->nullable();
            $table->string('cliestado')->nullable();
            $table->date('dtcri')->nullable();
            $table->string('status', 1)->nullable()->index();
            $table->string('tipstatus')->nullable();

            $table->foreign('idempresa')->references('empcontad')->on('empresas')->nullOnDelete();
            $table->foreign('idcliente')->references('contad')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};
