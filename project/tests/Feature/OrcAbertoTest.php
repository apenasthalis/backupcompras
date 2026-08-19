<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Orcamento;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrcAbertoTest extends TestCase
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

    private function makeOrcamento(User $cliente, Empresa $empresa, string $status = 'A'): Orcamento
    {
        return Orcamento::create([
            'idempresa' => $empresa->empcontad,
            'empnome' => $empresa->empnome,
            'empendereco' => $empresa->empendereco,
            'idcliente' => $cliente->contad,
            'clinome' => $cliente->name,
            'dtcri' => now()->toDateString(),
            'status' => $status,
        ]);
    }

    private function authHeaders(User $user): array
    {
        $token = app(JwtService::class)->generateToken($user);

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_listagem_mostra_orcamentos_abertos_em_uma_linha(): void
    {
        $cliente = $this->makeUser('C1');
        $outro = $this->makeUser('C2');
        $empresa = $this->makeEmpresa('E1');

        $orcAberto = $this->makeOrcamento($cliente, $empresa, 'A');
        $this->makeOrcamento($cliente, $empresa, 'A');
        $deOutro = $this->makeOrcamento($outro, $empresa, 'A');

        $response = $this->get('/orc-abertos', $this->authHeaders($cliente));

        $response->assertStatus(200);
        $response->assertSee('ORÇAMENTOS ABERTOS');
        $response->assertSee('Cliente C1');
        $response->assertSee('Rua Cliente C1, 100');
        $response->assertSee('Empresa Teste E1');
        $response->assertSee('>'.$orcAberto->idorc.'</td>', false);
        $response->assertDontSee('>'.$deOutro->idorc.'</td>', false);
        $response->assertDontSee('DESCRIÇÃO');
        $response->assertDontSee('Cimento');
    }

    public function test_cobrar_marca_orcamentos_selecionados_como_cobrado(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');

        $orc1 = $this->makeOrcamento($cliente, $empresa, 'A');
        $orc2 = $this->makeOrcamento($cliente, $empresa, 'A');

        $response = $this->post(
            '/orc-abertos/cobrar',
            ['orcamentos' => [$orc1->idorc, $orc2->idorc]],
            $this->authHeaders($cliente)
        );

        $response->assertRedirect(route('orc-abertos'));

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc1->idorc,
            'status' => 'C',
            'tipstatus' => 'Orçamento Cobrado',
        ]);
        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc2->idorc,
            'status' => 'C',
            'tipstatus' => 'Orçamento Cobrado',
        ]);
    }

    public function test_cobrar_exige_selecao(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $this->makeOrcamento($cliente, $empresa, 'A');

        $response = $this->post('/orc-abertos/cobrar', [], $this->authHeaders($cliente));

        $response->assertSessionHasErrors('orcamentos');
    }

    public function test_clique_na_linha_mantem_aberto_e_redireciona_para_editarorc(): void
    {
        $cliente = $this->makeUser('C1');
        $empresa = $this->makeEmpresa('E1');
        $orc = $this->makeOrcamento($cliente, $empresa, 'A');

        $response = $this->post(
            '/orc-abertos/'.$orc->idorc.'/editar',
            [],
            $this->authHeaders($cliente)
        );

        $response->assertRedirect(route('editar-orc', ['orcamento' => $orc->idorc]));

        $this->assertDatabaseHas('orcamentos', [
            'idorc' => $orc->idorc,
            'status' => 'A',
            'tipstatus' => 'Orçamento Aberto',
        ]);
    }

    public function test_rota_exige_autenticacao(): void
    {
        $response = $this->get('/orc-abertos');

        $response->assertRedirect(route('inicio'));
    }
}
