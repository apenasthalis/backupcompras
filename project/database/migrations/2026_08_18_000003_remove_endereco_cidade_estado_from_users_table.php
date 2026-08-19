<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'endereco')) {
                $table->dropColumn(['endereco', 'cidade', 'estado']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'endereco')) {
                $table->string('endereco')->nullable()->after('contad');
                $table->string('cidade')->nullable()->after('endereco');
                $table->string('estado')->nullable()->after('cidade');
            }
        });
    }
};