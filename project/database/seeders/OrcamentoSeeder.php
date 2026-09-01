<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Orcamento;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrcamentoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(EmpresaSeeder::class);

        $cliente = User::firstOrCreate(
            ['contad' => 'CDEMO'],
            [
                'name' => 'Cliente Demonstração',
                'email' => 'demo@exemplo.com',
                'password' => bcrypt('123456'),
                'plataforma' => 'Plataforma Demo',
                'sistema' => 'Sistema Demo',
                'endereco' => 'Rua da Entrega, 200',
                'cidade' => 'Goiatuba',
                'estado' => 'GO',
            ]
        );

        if (Orcamento::where('idcliente', $cliente->contad)->exists()) {
            return;
        }

        $empresas = Empresa::where('empstatus', 'ativo')->get();
        $empA = $empresas->first() ?? Empresa::first();
        $empB = $empresas->skip(1)->first() ?? $empA;

        $abertos = [
            ['emp' => $empA, 'tip' => 'Orçamento Aberto'],
            ['emp' => $empB, 'tip' => 'Orçamento Aberto'],
            ['emp' => $empA, 'tip' => 'Orçamento Aberto'],
        ];

        foreach ($abertos as $item) {
            Orcamento::create([
                'idempresa' => $item['emp']->empcontad,
                'empnome' => $item['emp']->empnome,
                'empendereco' => $item['emp']->empendereco,
                'empcidade' => $item['emp']->empcidade,
                'empestado' => $item['emp']->empestado,
                'idcliente' => $cliente->contad,
                'clinome' => $cliente->name,
                'cliendereco' => $cliente->endereco,
                'clicidade' => $cliente->cidade,
                'cliestado' => $cliente->estado,
                'dtcri' => now()->toDateString(),
                'status' => 'A',
                'tipstatus' => $item['tip'],
            ]);
        }

        $prontos = [
            ['emp' => $empB, 'tip' => 'Orçamento Pronto'],
            ['emp' => $empA, 'tip' => 'Orçamento Pronto'],
        ];

        foreach ($prontos as $item) {
            Orcamento::create([
                'idempresa' => $item['emp']->empcontad,
                'empnome' => $item['emp']->empnome,
                'empendereco' => $item['emp']->empendereco,
                'empcidade' => $item['emp']->empcidade,
                'empestado' => $item['emp']->empestado,
                'idcliente' => $cliente->contad,
                'clinome' => $cliente->name,
                'cliendereco' => $cliente->endereco,
                'clicidade' => $cliente->cidade,
                'cliestado' => $cliente->estado,
                'dtcri' => now()->toDateString(),
                'status' => 'P',
                'tipstatus' => $item['tip'],
            ]);
        }
    }
}
