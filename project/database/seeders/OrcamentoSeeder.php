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

        $empresa = Empresa::where('empstatus', 'ativo')->first() ?? Empresa::first();

        if (!$empresa) {
            return;
        }

        $temAberto = Orcamento::query()
            ->where('idcliente', $cliente->contad)
            ->where('status', 'A')
            ->exists();

        if ($temAberto) {
            return;
        }

        for ($i = 0; $i < 3; $i++) {
            Orcamento::create([
                'idempresa' => $empresa->empcontad,
                'empnome' => $empresa->empnome,
                'empendereco' => $empresa->empendereco,
                'empcidade' => $empresa->empcidade,
                'empestado' => $empresa->empestado,
                'idcliente' => $cliente->contad,
                'clinome' => $cliente->name,
                'cliendereco' => $cliente->endereco,
                'clicidade' => $cliente->cidade,
                'cliestado' => $cliente->estado,
                'dtcri' => now()->toDateString(),
                'status' => 'A',
                'tipstatus' => 'Orçamento Aberto',
                'desconto' => 0,
            ]);
        }
    }
}
