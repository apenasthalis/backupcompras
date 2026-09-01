# Testes Automatizados

## Estrutura de Branches e Banco de Dados

- **`main`**: branch de teste. Usa o banco `compras_teste` (PostgreSQL).
- **Demais branches**: desenvolvimento de features. Cada uma pode criar seu próprio banco de teste apontando o `.env` local para um banco diferente.

## Arquivos de Ambiente (.env)

### `.env_producao` — Usar em produção

Configuração para o servidor de produção (`https://163.176.218.152`).

**Quando usar:** no servidor de produção, renomeie para `.env` ou copie o conteúdo. Banco PostgreSQL `compras` no container `postgres`.

### `.env_teste` — Usar na branch main para testar localmente

Configuração para testes manuais com PostgreSQL.

**Quando usar:** na branch `main` para testar localmente com o banco `compras_teste`. Renomeie para `.env` ou copie o conteúdo. Os testes do GitHub Actions usam SQLite em memória (configurado no `phpunit.xml`), não dependem deste arquivo.

### `.env` (atual) — Usar em desenvolvimento local

Use o `.env.example` como base para criar seu `.env` de desenvolvimento local. O `phpunit.xml` já configura SQLite em memória para os testes automatizados.

## Como Executar os Testes

### Localmente (SQLite em memória)

```bash
cd project
cp .env.example .env
php artisan key:generate
php artisan test
```

### Com PostgreSQL (teste manual)

```bash
cd project
cp .env_teste .env
php artisan migrate --force
php artisan test
```

### Via GitHub Actions

A cada push ou pull request para `main`, o workflow `.github/workflows/tests.yml` executa automaticamente:
1. Instala o PHP 8.3 e dependências
2. Gera a APP_KEY
3. Sobe um PostgreSQL 17 como serviço
4. Executa as migrations
5. Roda `php artisan test`

Para ver os resultados, acesse a aba **Actions** do repositório no GitHub.

## Criando Testes

Os testes ficam em `project/tests/`:

- `tests/Unit/` — Testes de unidade (não dependem do Laravel)
- `tests/Feature/` — Testes de feature (com roteamento, banco, etc.)

### Exemplo de teste de feature:

```php
namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_returns_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@teste.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type']);
    }
}
```

## Fluxo de Trabalho Recomendado

1. Crie uma branch a partir da `main` para desenvolver uma feature
2. Execute `php artisan test` localmente (usa SQLite em memória)
3. Faça commit e push
4. Abra um Pull Request para a `main`
5. O GitHub Actions executa os testes automaticamente
6. Se os testes passarem, faça o merge

## Checklist para Novo Ambiente

- [ ] `cp .env.example .env`
- [ ] `php artisan key:generate`
- [ ] Configurar `DB_*` no `.env`
- [ ] `php artisan migrate --force`
- [ ] `php artisan test`
