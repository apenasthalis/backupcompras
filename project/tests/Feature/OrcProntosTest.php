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

    private function makeProductFor(User $user, string $description, ?Orcamento $orcamento = null): UserProduct
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

    public function test_rota_exige_autenticacao(): void
    {
        $response = $this->get('/orc-prontos');

        $response->assertRedirect(route('inicio'));
    }
}