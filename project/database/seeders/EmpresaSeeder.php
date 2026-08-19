<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $empresas = [
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

        foreach ($empresas as $empcontad => $dados) {
            Empresa::updateOrCreate(
                ['empcontad' => $empcontad],
                array_merge($dados, ['empconta' => $empcontad, 'empstatus' => 'ativo'])
            );
        }
    }
}