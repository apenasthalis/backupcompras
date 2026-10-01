<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->decimal('preco_lojista', 12, 2)->nullable()->default(0);
            $table->decimal('preco_cliente', 12, 2)->nullable()->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropColumn(['preco_lojista', 'preco_cliente']);
        });
    }
};