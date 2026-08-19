<?php

use App\Models\Empresa;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const EMPRESAS = [
        'JOHN DEERE' => [
            'empnome' => 'John Deere',
            'empemail' => 'contato@johndeere.com.br',
            'empendereco' => 'Av. Estados, 1200',
            'empcidade' => 'Catalão',
            'empestado' => 'GO',
        ],
        'IRMAO SOARES' => [
            'empnome' => 'Irmão Soares',
            'empemail' => 'contato@irmaosoares.com.br',
            'empendereco' => 'Av. Brasil, 500',
            'empcidade' => 'Goiatuba',
            'empestado' => 'GO',
        ],
    ];

    public function up(): void
    {
        foreach (self::EMPRESAS as $empcontad => $dados) {
            Empresa::updateOrCreate(
                ['empcontad' => $empcontad],
                array_merge($dados, ['empconta' => $empcontad, 'empstatus' => 'ativo'])
            );
        }
    }

    public function down(): void
    {
        Empresa::whereIn('empcontad', array_keys(self::EMPRESAS))->delete();
    }
};