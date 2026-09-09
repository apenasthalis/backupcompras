<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Segmento;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresasTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = app(JwtService::class)->generateToken($user);

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_empresas_fixas_sao_seedadas(): void
    {
        $this->assertDatabaseHas('empresas', [
            'empcontad' => 'JOHN DEERE',
            'empnome' => 'John Deere',
            'empstatus' => 'ativo',
        ]);

        $this->assertDatabaseHas('empresas', [
            'empcontad' => 'IRMAO SOARES',
            'empnome' => 'Irmão Soares',
            'empstatus' => 'ativo',
        ]);
    }

    public function test_api_lista_empresas_fixas_ativas(): void
    {
        $empresa = Empresa::where('empcontad', 'JOHN DEERE')->first();
        $empresa->update(['empstatus' => 'bloqueado']);

        $user = User::create([
            'name' => 'Cliente',
            'email' => 'cliente@teste.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->getJson('/api/empresas', $this->authHeaders($user));

        $response->assertStatus(200);
        $response->assertJsonFragment(['empcontad' => 'IRMAO SOARES', 'empnome' => 'Irmão Soares']);
        $response->assertJsonMissing(['empcontad' => 'JOHN DEERE']);
    }

    public function test_rota_exige_autenticacao(): void
    {
        $response = $this->getJson('/api/empresas');

        $response->assertStatus(401);
    }

    public function test_api_lista_empresas_da_cidade_do_comprador(): void
    {
        $empresa = Empresa::where('empcontad', 'IRMAO SOARES')->first();
        $empresa->update(['empcidade' => 'Acreúna', 'empestado' => 'GO']);

        Empresa::create([
            'empconta' => 'OUTRA CIDADE',
            'empcontad' => 'OUTRA CIDADE',
            'empnome' => 'Outra Cidade',
            'empcidade' => 'Anápolis',
            'empestado' => 'GO',
            'empstatus' => 'ativo',
        ]);

        $user = User::create([
            'name' => 'Cliente Acreúna',
            'email' => 'cliente-acreuna@teste.com',
            'password' => bcrypt('123456'),
            'cidade' => 'Acreúna',
            'estado' => 'GO',
        ]);

        $response = $this->getJson('/api/empresas', $this->authHeaders($user));

        $response->assertStatus(200);
        $response->assertJsonFragment(['empcontad' => 'IRMAO SOARES']);
        $response->assertJsonMissing(['empcontad' => 'OUTRA CIDADE']);
        $response->assertJsonMissing(['empcontad' => 'JOHN DEERE']);
    }

    public function test_api_filtra_empresas_por_segmento(): void
    {
        $farmacia = Segmento::create(['name' => 'Farmácia']);
        $eletrica = Segmento::create(['name' => 'Material Elétrico']);

        Empresa::create([
            'empconta' => 'FARMACIA TESTE',
            'empcontad' => 'FARMACIA TESTE',
            'empnome' => 'Farmácia Teste',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento_id' => $farmacia->id,
            'empstatus' => 'ativo',
        ]);

        Empresa::create([
            'empconta' => 'ELETRICA TESTE',
            'empcontad' => 'ELETRICA TESTE',
            'empnome' => 'Elétrica Teste',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento_id' => $eletrica->id,
            'empstatus' => 'ativo',
        ]);

        $user = User::create([
            'name' => 'Cliente',
            'email' => 'cliente-filtro@teste.com',
            'password' => bcrypt('123456'),
            'cidade' => 'Acreúna',
            'estado' => 'GO',
        ]);

        $response = $this->getJson('/api/empresas?segmento_id='.$farmacia->id, $this->authHeaders($user));

        $response->assertStatus(200);
        $response->assertJsonFragment(['empcontad' => 'FARMACIA TESTE']);
        $response->assertJsonMissing(['empcontad' => 'ELETRICA TESTE']);
    }

    public function test_api_lista_todos_segmentos_da_tabela(): void
    {
        Segmento::create(['name' => 'Farmácia']);
        Segmento::create(['name' => 'Material Elétrico']);
        Segmento::create(['name' => 'Gás']);

        $user = User::create([
            'name' => 'Cliente',
            'email' => 'cliente-segmentos@teste.com',
            'password' => bcrypt('123456'),
            'cidade' => 'Acreúna',
            'estado' => 'GO',
        ]);

        $response = $this->getJson('/api/empresas/segmentos', $this->authHeaders($user));

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Farmácia']);
        $response->assertJsonFragment(['name' => 'Material Elétrico']);
        $response->assertJsonFragment(['name' => 'Gás']);
    }
}
