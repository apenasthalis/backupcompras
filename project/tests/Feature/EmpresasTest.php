<?php

namespace Tests\Feature;

use App\Models\Empresa;
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
}