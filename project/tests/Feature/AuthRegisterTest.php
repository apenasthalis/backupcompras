<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastro_salva_endereco_de_entrega_na_tabela_users(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Novo Cliente',
            'email' => 'cliente@teste.com',
            'password' => '123456',
            'endereco' => 'Rua Teste, 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.name', 'Novo Cliente')
            ->assertJsonPath('user.email', 'cliente@teste.com');

        $this->assertDatabaseHas('users', [
            'email' => 'cliente@teste.com',
            'name' => 'Novo Cliente',
            'endereco' => 'Rua Teste, 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        $user = User::where('email', 'cliente@teste.com')->first();
        $this->assertNotNull($user->contad);
    }

    public function test_cadastro_nao_cria_empresa(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Novo Cliente',
            'email' => 'cliente@teste.com',
            'password' => '123456',
            'endereco' => 'Rua Teste, 123',
        ])->assertStatus(201);

        $this->assertDatabaseMissing('empresas', [
            'empemail' => 'cliente@teste.com',
        ]);
    }

    public function test_cadastro_nao_exige_endereco_cidade_e_estado(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Novo Cliente',
            'email' => 'cliente2@teste.com',
            'password' => '123456',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'cliente2@teste.com']);
    }
}