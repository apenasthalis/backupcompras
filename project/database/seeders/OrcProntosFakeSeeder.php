<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Orcamento;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Database\Seeder;

/**
 * Dois orçamentos artificiais (status P) para a tela de Orçamentos Prontos.
 * Pode ser executado quantas vezes quiser: os registros criados por ele são
 * apagados e recriados sempre com os mesmos valores.
 *
 * O cliente alvo é o CDEMO por padrão. Para gerar os dados em outra conta
 * (a listagem só mostra orçamentos do cliente logado), informe o contad
 * ou o e-mail do usuário:
 *
 * ORC_TESTE_CLIENTE=1thalisgabriel1@gmail.com php artisan db:seed --class=OrcProntosFakeSeeder --force
 */
class OrcProntosFakeSeeder extends Seeder
{
    private const CONTAD_CLIENTE = 'CDEMO';

    private const TIPSTATUS = 'Orçamento Pronto (Fake)';

    private const TIPSTATUS_ANTERIOR = 'Orçamento Pronto (Teste)';

    private const MARGEM_CLIENTE = 0.03;

    private const ORCAMENTOS = [
        [
            'empcontad' => 'MATERIAL DE CONSTRUCAO ACREUNA',
            'desconto' => 0.00,
            'itens' => [
                ['Cimento CP-II 50kg', 'Votorantim', 'SC', 10, 48.90],
                ['Areia Média m³', 'Areia Bom', 'M3', 5, 320.00],
                ['Tijolo 6 Furos', 'Cerâmica Paulista', 'UN', 500, 1.25],
            ],
        ],
        [
            'empcontad' => 'AREIAL ACREUNA',
            'desconto' => 3.50,
            'itens' => [
                ['Tubo PVC 100mm 6m', 'Poliestirano', 'UN', 20, 89.90],
                ['Joelho PVC 100mm', 'Poliestirano', 'UN', 30, 12.40],
                ['Torneira PVC 1/2"', 'Deca', 'UN', 15, 47.90],
            ],
        ],
    ];

    public function run(): void
    {
        $this->call(EmpresaSeeder::class);

        $this->apagarAnteriores();

        $cliente = $this->clienteAlvo();

        foreach (self::ORCAMENTOS as $dados) {
            $orcamento = $this->criarOrcamento($cliente, $this->empresa($dados['empcontad']), $dados['desconto']);

            foreach ($dados['itens'] as [$descricao, $marca, $unidade, $quantidade, $precoLojista]) {
                UserProduct::create([
                    'product_id' => Product::firstOrCreate(['name' => $descricao])->id,
                    'user_id' => $cliente->id,
                    'orcamento_id' => $orcamento->idorc,
                    'description' => $descricao,
                    'brand' => $marca,
                    'unit' => $unidade,
                    'quantity' => $quantidade,
                    'preco_lojista' => $precoLojista,
                    'preco_cliente' => round($precoLojista * (1 + self::MARGEM_CLIENTE), 2),
                ]);
            }
        }
    }

    private function clienteAlvo(): User
    {
        $referencia = getenv('ORC_TESTE_CLIENTE') ?: self::CONTAD_CLIENTE;

        $cliente = User::query()
            ->where('contad', $referencia)
            ->orWhere('email', $referencia)
            ->first();

        if ($cliente) {
            return $cliente;
        }

        return User::firstOrCreate(
            ['contad' => self::CONTAD_CLIENTE],
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
    }

    private function empresa(string $empcontad): Empresa
    {
        return Empresa::firstWhere('empcontad', $empcontad)
            ?? Empresa::where('empstatus', 'ativo')->firstOrFail();
    }

    private function apagarAnteriores(): void
    {
        $ids = Orcamento::query()
            ->whereIn('tipstatus', [self::TIPSTATUS, self::TIPSTATUS_ANTERIOR])
            ->pluck('idorc');

        if ($ids->isEmpty()) {
            return;
        }

        UserProduct::query()->whereIn('orcamento_id', $ids)->delete();
        Orcamento::query()->whereIn('idorc', $ids)->delete();
    }

    private function criarOrcamento(User $cliente, Empresa $empresa, float $desconto): Orcamento
    {
        return Orcamento::create([
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
            'status' => 'P',
            'tipstatus' => self::TIPSTATUS,
            'desconto' => $desconto,
        ]);
    }
}
