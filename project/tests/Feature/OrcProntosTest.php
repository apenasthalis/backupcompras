<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Orcamento;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrcProntosTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $contad): User
    {
        return User::create([
            'name' => 'Cliente '.$contad,
            'email' => $contad.'@teste.com',
            'password' => bcrypt('123456'),
            'plataforma' => 'Plataforma Teste',
            'sistema' => 'Sistema Teste',
            'contad' => $contad,
            'endereco' => 'Rua Cliente '.$contad.', 100',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);
    }

    private function makeEmpresa(string $empcontad): Empresa
    {
        return Empresa::create([
            'empconta' => $empcontad,
            'empcontad' => $empcontad,
            'empnome' => 'Empresa Teste '.$empcontad,
            'empendereco' => 'Rua Exemplo, 12345',
            'empcidade' => 'São Paulo',
            'empestado' => 'SP',
            'empstatus' => 'ativo',
        ]);
    }

    private function makeOrcamento(User $cliente, Empresa $empresa, string $status = 'P'): Orcamento
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
            'status' => $status,
            'tipstatus' => 'Orçamento Pronto',
        ]);
    }

    private function makeProductFor(User $user, string $description, ?Orcamento $orcamento = null, float $precoLojista = 0.0, float $precoCliente = 0.0): UserProduct
    {
        $product = Product::create(['name' => $description]);

        return UserProduct::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'orcamento_id' => $orcamento?->idorc,
            'description' => $description,
            'brand' => 'Marca',
            'unit' => 'UN',
            'quantity' => 2,
            'preco_lojista' => $precoLojista,
            'preco_cliente' => $precoCliente,
        ]);
    }

    private function authHeaders(User $user): array
    {
        $token = app(JwtService::class)->generateToken($user);

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_lista_somente_orcamentos_prontos_do_cliente_logado(): void
    {
        $cliente = $this->makeUser('C1');
        $outro = $this->makeUser('C2');
        $empresa = $this->makeEmpresa('E1');

        $pronto = $this->makeOrcamento($cliente, $empresa, 'P');
        $aberto = $this->makeOrcamento($cliente, $empresa, 'A');
        $deOutro = $this->makeOrcamento($outro, $empresa, 'P');

        $response = $this->get('/orc-prontos', $this->authHeaders($cliente));

        $response->assertStatus(200);
        $response->assertSee('ORÇAMENTOS PRONTOS');
        $response->assertSee('Cliente C1');
        $response->assertSee('Empresa Teste E1');
        $response->assertSee('>'.$pronto->idorc.'</td>', false);
        $response->assertDontSee('>'.$aberto->idorc.'</td>', false);
        $response->assertDontSee('>'.$deOutro->idorc.'</td>', false);
    }

    public function test_aprovar_mostra_orcamento_e_seus_itens(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $this->makeProductFor($cliente, 'Cimento 50kg', $orc);

        $response = $this->get('/orc-prontos/'.$orc->idorc.'/aprovar', $this->authHeaders($cliente));

        $response->assertStatus(200);
        $response->assertSee('APROVAR ORÇAMENTO');
        $response->assertSee('Cimento 50kg');
        $response->assertSee('>'.$orc->idorc.'</td>', false);
    }

    public function test_api_cria_orcamento_pronto_com_dados_do_cliente(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');

        $response = $this->postJson('/api/orcamentos', ['idempresa' => $empresa->empcontad], $this->authHeaders($cliente));

        $response->assertStatus(201);
        $response->assertJsonPath('orcamento.idempresa', $empresa->empcontad);
        $response->assertJsonPath('orcamento.empnome', $empresa->empnome);
        $response->assertJsonPath('orcamento.clinome', 'Cliente C1');
        $response->assertJsonPath('orcamento.status', 'A');

        $this->assertDatabaseHas('orcamentos', [
            'idcliente' => 'C1',
            'idempresa' => $empresa->empcontad,
            'clinome' => 'Cliente C1',
            'empendereco' => 'Rua Exemplo, 12345',
            'cliendereco' => 'Rua Cliente C1, 100',
            'clicidade' => 'São Paulo',
            'cliestado' => 'SP',
            'status' => 'A',
            'tipstatus' => 'Orçamento Aberto',
        ]);
    }

    public function test_api_cria_orcamento_para_usuario_sem_contad(): void
    {
        $cliente = User::create([
            'name' => 'Cliente Sem Contad',
            'email' => 'semcontad@teste.com',
            'password' => bcrypt('123456'),
            'endereco' => 'Rua Entrega, 55',
            'cidade' => 'Goiatuba',
            'estado' => 'GO',
        ]);
        $empresa = $this->makeEmpresa('E1');

        $response = $this->postJson('/api/orcamentos', ['idempresa' => $empresa->empcontad], $this->authHeaders($cliente));

        $response->assertStatus(201);
        $response->assertJsonPath('orcamento.cliendereco', 'Rua Entrega, 55');

        $cliente->refresh();
        $this->assertNotNull($cliente->contad);
        $this->assertDatabaseHas('orcamentos', ['idcliente' => $cliente->contad]);
    }

    public function test_api_exige_idempresa(): void
    {
        $cliente = $this->makeUser('C1');

        $response = $this->postJson('/api/orcamentos', [], $this->authHeaders($cliente));

        $response->assertStatus(422);
    }

    public function test_api_vincula_item_ao_orcamento(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $product = Product::create(['name' => 'Produto 1']);

        $response = $this->postJson('/api/user-products', [
            'product_id' => $product->id,
            'orcamento_id' => $orc->idorc,
            'description' => 'Cimento 50kg',
            'brand' => 'Marca',
            'unit' => 'UN',
            'quantity' => 2,
        ], $this->authHeaders($cliente));

        $response->assertStatus(201);
        $response->assertJsonPath('orcamento_id', $orc->idorc);

        $this->assertDatabaseHas('user_products', [
            'user_id' => $cliente->id,
            'orcamento_id' => $orc->idorc,
            'description' => 'Cimento 50kg',
            'quantity' => 2.00,
        ]);
    }

    public function test_listagem_mostra_preco_lojista_e_preco_cliente_de_cada_orcamento(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);
        $this->makeProductFor($cliente, 'Areia Media', $orc, 10.00, 10.30);

        $response = $this->get('/orc-prontos', $this->authHeaders($cliente));

        $response->assertOk();
        $response->assertSee('PREÇO LOJISTA');
        $response->assertSee('PREÇO CLIENTE');
        $response->assertSee('R$ 117,80');
        $response->assertSee('R$ 121,34');
    }

    public function test_pagina_de_aprovacao_permite_editar_precos_e_desconto(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->get('/orc-prontos/'.$orc->idorc.'/aprovar', $this->authHeaders($cliente));

        $response->assertOk();
        $response->assertSee('PREÇO LOJISTA');
        $response->assertSee('PREÇO CLIENTE');
        $response->assertSee('Desconto (%)');
        $response->assertSee('preco_lojista]" value="48.90"', false);
        $response->assertSee('preco_cliente]" value="50.37"', false);
        $response->assertSee('value="'.$item->id.'"', false);
    }

    public function test_salvar_atualiza_precos_desconto_e_itens(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/salvar', [
            'desconto' => 10,
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento CP-II 50kg',
                    'brand' => 'Votorantim',
                    'unit' => 'SC',
                    'quantity' => 4,
                    'preco_lojista' => 100.00,
                    'preco_cliente' => 103.00,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertRedirect(route('orc-prontos.aprovar', ['orcamento' => $orc->idorc]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_products', [
            'id' => $item->id,
            'description' => 'Cimento CP-II 50kg',
            'brand' => 'Votorantim',
            'quantity' => 4,
            'preco_lojista' => 100.00,
            'preco_cliente' => 103.00,
        ]);

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'desconto' => 10,
        ]);
    }

    public function test_salvar_rejeita_desconto_acima_de_cem_por_cento(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/salvar', [
            'desconto' => 150,
        ], $this->authHeaders($cliente));

        $response->assertSessionHasErrors('desconto');
        $this->assertDatabaseHas('orcamentos', ['idorc' => $orc->idorc, 'desconto' => 0]);
    }

    public function test_salvar_exige_precos_em_cada_item(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/salvar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 2,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertSessionHasErrors('itens.0.preco_lojista');
        $response->assertSessionHasErrors('itens.0.preco_cliente');
    }

    public function test_salvar_nao_altera_item_de_outro_orcamento(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');
        $outro = $this->makeOrcamento($cliente, $empresa, 'P');

        $itemDoOutro = $this->makeProductFor($cliente, 'Tijolo', $outro, 1.25, 1.29);

        $this->post('/orc-prontos/'.$orc->idorc.'/salvar', [
            'itens' => [
                [
                    'id' => $itemDoOutro->id,
                    'description' => 'HACK',
                    'quantity' => 1,
                    'preco_lojista' => 0.01,
                    'preco_cliente' => 0.01,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('user_products', [
            'id' => $itemDoOutro->id,
            'description' => 'Tijolo',
            'preco_lojista' => 1.25,
        ]);
    }

    public function test_salvar_recusa_orcamento_de_outro_cliente(): void
    {
        $cliente = $this->makeUser('C1');
        $outro = $this->makeUser('C2');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($outro, $empresa, 'P');

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/salvar', [
            'desconto' => 5,
        ], $this->authHeaders($cliente));

        $response->assertNotFound();
        $this->assertDatabaseHas('orcamentos', ['idorc' => $orc->idorc, 'desconto' => 0]);
    }

    public function test_totais_aplicam_o_desconto_do_orcamento(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');
        $orc->update(['desconto' => 10]);

        $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 100.00, 103.00);

        $orc->load('itens');

        $this->assertEquals(200.00, $orc->totalLojista());
        $this->assertEquals(206.00, $orc->totalCliente());
        $this->assertEquals(185.40, $orc->totalFinal());
    }

    public function test_rota_exige_autenticacao(): void
    {
        $response = $this->get('/orc-prontos');

        $response->assertRedirect(route('inicio'));
    }
}