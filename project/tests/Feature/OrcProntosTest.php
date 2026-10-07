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

    public function test_listagem_de_prontos_nao_mostra_quadrinho_de_selecao(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $this->makeOrcamento($cliente, $empresa, 'P');

        $response = $this->get('/orc-prontos', $this->authHeaders($cliente));

        $response->assertOk();
        $response->assertDontSee('type="checkbox"', false);
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
        $response->assertSee('APROVAR ORÇAMENTO Nº '.$orc->idorc);
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

    public function test_listagem_mostra_preco_final_de_cada_orcamento(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);
        $this->makeProductFor($cliente, 'Areia Media', $orc, 10.00, 10.30);

        $response = $this->get('/orc-prontos', $this->authHeaders($cliente));

        $response->assertOk();
        $response->assertSee('PREÇO');
        $response->assertSee('R$ 121,34');
        $response->assertDontSee('PREÇO LOJISTA');
        $response->assertDontSee('R$ 117,80');
    }

    public function test_pagina_de_aprovacao_mostra_preco_unit_total_desconto_frete_e_botoes(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->get('/orc-prontos/'.$orc->idorc.'/aprovar', $this->authHeaders($cliente));

        $response->assertOk();
        $response->assertSee('PREÇO UNIT');
        $response->assertSee('PREÇO TOTAL');
        $response->assertSee('Desconto (%)');
        $response->assertSee('Solicitar Mais desconto');
        $response->assertSee('name="solicitar_desconto"', false);
        $response->assertSee('readonly', false);
        $response->assertDontSee('name="desconto"', false);
        $response->assertSee('Frete:');
        $response->assertSee('Adicionar produto:');
        $response->assertSee('>Avançar<', false);
        $response->assertSee('>Voltar<', false);
        $response->assertDontSee('Salvar Alterações');
        $response->assertSee('EMPRESA: '.$empresa->empnome);
        $response->assertSee('CLIENTE: '.$cliente->name);
        $response->assertSee('APROVAR ORÇAMENTO Nº '.$orc->idorc);
        $response->assertSee('"id":'.$item->id, false);
        $response->assertDontSee('PREÇO LOJISTA');
        $response->assertDontSee('PREÇO CLIENTE');
        $response->assertSee('"preco":"50.37"', false);
        $response->assertSee('"qty":"2"', false);
    }

    public function test_avancar_sem_alteracao_manda_para_cobranca(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'brand' => 'Marca',
                    'unit' => 'UN',
                    'quantity' => 2,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertRedirect(route('orc-abertos'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'C',
            'tipstatus' => 'Orçamento Cobrado',
        ]);
    }

    public function test_avancar_com_item_incluido_marca_status_modificado(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 2,
                ],
                [
                    'description' => 'Areia Media',
                    'brand' => 'Marca',
                    'unit' => 'SC',
                    'quantity' => 3,
                    'preco_cliente' => 12.50,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertRedirect(route('orc-prontos'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'M',
            'tipstatus' => 'Orçamento Modificado',
        ]);

        $this->assertDatabaseHas('user_products', [
            'orcamento_id' => $orc->idorc,
            'description' => 'Areia Media',
            'unit' => 'SC',
            'quantity' => 3,
            'preco_cliente' => 12.50,
        ]);
    }

    public function test_avancar_com_quantidade_aumentada_marca_status_modificado(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 5,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('user_products', [
            'id' => $item->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'M',
            'tipstatus' => 'Orçamento Modificado',
        ]);
    }

    public function test_avancar_com_quantidade_reduzida_nao_marca_modificado(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 1,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('user_products', [
            'id' => $item->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'C',
            'tipstatus' => 'Orçamento Cobrado',
        ]);
    }

    public function test_avancar_apagando_item_nao_marca_modificado(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);
        $remover = $this->makeProductFor($cliente, 'Areia Media', $orc, 10.00, 10.30);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 2,
                ],
            ],
            'deletar' => [$remover->id],
        ], $this->authHeaders($cliente));

        $response->assertRedirect(route('orc-abertos'));

        $this->assertDatabaseMissing('user_products', ['id' => $remover->id]);

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'C',
            'tipstatus' => 'Orçamento Cobrado',
        ]);
    }

    public function test_avancar_com_o_quadrinho_marcado_marca_desconto_solicitado(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'solicitar_desconto' => 1,
            'frete' => 25.50,
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 2,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertRedirect(route('orc-prontos'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'D',
            'tipstatus' => 'Desconto Solicitado',
            'frete' => 25.50,
        ]);
    }

    public function test_avancar_sem_marcar_o_quadrinho_manda_para_cobranca(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');
        $orc->update(['desconto' => 10]);

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 2,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertRedirect(route('orc-abertos'));

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'C',
            'tipstatus' => 'Orçamento Cobrado',
            'desconto' => 10,
        ]);
    }

    public function test_avancar_nao_altera_o_desconto_ja_concedido(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');
        $orc->update(['desconto' => 10]);

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'desconto' => 80,
            'solicitar_desconto' => 1,
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 2,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'D',
            'desconto' => 10,
        ]);
    }

    public function test_avancar_com_item_novo_tem_precedencia_sobre_o_desconto(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'solicitar_desconto' => 1,
            'itens' => [
                [
                    'description' => 'Tijolo Baiano',
                    'quantity' => 10,
                    'preco_cliente' => 1.29,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'M',
            'tipstatus' => 'Orçamento Modificado',
        ]);
    }

    public function test_avancar_modificado_grava_que_o_cliente_solicitou_desconto(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'solicitar_desconto' => 1,
            'itens' => [
                [
                    'description' => 'Tijolo Baiano',
                    'quantity' => 10,
                    'preco_cliente' => 1.29,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'M',
            'tipstatus' => 'Orçamento Modificado',
            'solicita_desconto' => true,
        ]);
    }

    public function test_avancar_sem_o_quadrinho_grava_solicitacao_de_desconto_falso(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'Cimento 50kg',
                    'quantity' => 2,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'C',
            'solicita_desconto' => false,
        ]);
    }

    public function test_avancar_com_item_digitado_manualmente_cria_o_produto(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'product_id' => '',
                    'description' => 'Tijolo Baiano',
                    'quantity' => 10,
                    'preco_cliente' => 1.29,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertRedirect(route('orc-prontos'));

        $produto = Product::query()->where('name', 'Tijolo Baiano')->first();

        $this->assertNotNull($produto);
        $this->assertDatabaseHas('user_products', [
            'orcamento_id' => $orc->idorc,
            'product_id' => $produto->id,
            'description' => 'Tijolo Baiano',
            'preco_cliente' => 1.29,
        ]);
    }

    public function test_avancar_nao_altera_preco_cliente_nos_itens_ja_existentes(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                    'description' => 'HACK',
                    'brand' => 'HACK',
                    'unit' => 'HACK',
                    'quantity' => 3,
                    'preco_cliente' => 2.00,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('user_products', [
            'id' => $item->id,
            'description' => 'Cimento 50kg',
            'brand' => 'Marca',
            'unit' => 'UN',
            'quantity' => 3,
            'preco_cliente' => 50.37,
        ]);
    }

    public function test_avancar_rejeita_frete_negativo(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'frete' => -10,
            'itens' => [
                ['description' => 'Cimento 50kg', 'quantity' => 2],
            ],
        ], $this->authHeaders($cliente));

        $response->assertSessionHasErrors('frete');
        $this->assertDatabaseHas('orcamentos', ['idorc' => $orc->idorc, 'status' => 'P']);
    }

    public function test_avancar_exige_pelo_menos_um_item(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [],
        ], $this->authHeaders($cliente));

        $response->assertSessionHasErrors('itens');
        $this->assertDatabaseHas('orcamentos', ['idorc' => $orc->idorc, 'status' => 'P']);
    }

    public function test_avancar_exige_quantidade_em_cada_item(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');

        $item = $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 48.90, 50.37);

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $item->id,
                ],
            ],
        ], $this->authHeaders($cliente));

        $response->assertSessionHasErrors('itens.0.quantity');
        $this->assertDatabaseHas('orcamentos', ['idorc' => $orc->idorc, 'status' => 'P']);
    }

    public function test_avancar_nao_altera_item_de_outro_orcamento(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');
        $outro = $this->makeOrcamento($cliente, $empresa, 'P');

        $itemDoOutro = $this->makeProductFor($cliente, 'Tijolo', $outro, 1.25, 1.29);

        $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'itens' => [
                [
                    'id' => $itemDoOutro->id,
                    'description' => 'Tijolo',
                    'quantity' => 9,
                ],
            ],
        ], $this->authHeaders($cliente));

        $this->assertDatabaseHas('user_products', [
            'id' => $itemDoOutro->id,
            'quantity' => 2,
            'preco_lojista' => 1.25,
        ]);
    }

    public function test_avancar_recusa_orcamento_de_outro_cliente(): void
    {
        $cliente = $this->makeUser('C1');
        $outro = $this->makeUser('C2');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($outro, $empresa, 'P');

        $response = $this->post('/orc-prontos/'.$orc->idorc.'/avancar', [
            'solicitar_desconto' => 1,
            'itens' => [
                ['description' => 'Cimento 50kg', 'quantity' => 2],
            ],
        ], $this->authHeaders($cliente));

        $response->assertNotFound();
        $this->assertDatabaseHas('orcamentos', ['idorc' => $orc->idorc, 'desconto' => 0, 'status' => 'P']);
    }

    public function test_totais_aplicam_o_desconto_e_somam_o_frete(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'P');
        $orc->update(['desconto' => 10, 'frete' => 30.00]);

        $this->makeProductFor($cliente, 'Cimento 50kg', $orc, 100.00, 103.00);

        $orc->load('itens');

        $this->assertEquals(200.00, $orc->totalLojista());
        $this->assertEquals(206.00, $orc->totalCliente());
        $this->assertEquals(215.40, $orc->totalFinal());
    }

    public function test_rota_exige_autenticacao(): void
    {
        $response = $this->get('/orc-prontos');

        $response->assertRedirect(route('inicio'));
    }
}