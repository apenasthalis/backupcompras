<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->unsignedBigInteger('orcamento_id')->nullable()->after('user_id');

            $table->index('orcamento_id');
            $table->foreign('orcamento_id')
                ->references('idorc')
                ->on('orcamentos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropForeign(['orcamento_id']);
            $table->dropIndex(['orcamento_id']);
            $table->dropColumn('orcamento_id');
        });
    }
};