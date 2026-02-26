# Central de Doacoes - Paroquia Santa Bernadete

Sistema web para gestao e divulgacao de mantimentos prioritarios para apoio as familias afetadas por enchentes em Uba/MG.

## Stack

- Laravel 12 (PHP 8.2+)
- Blade + HTML/CSS/JavaScript puro
- MySQL 8+ (producao)

## Funcionalidades

- Pagina publica `/` com lista ordenada por prioridade:
  - `vermelho` (critico)
  - `amarelo` (moderado)
  - `abastecido`
- Pagina publica `/doar` com:
  - endereco
  - botao para Google Maps
  - chave PIX com botao de copia
- Painel admin `/admin` com autenticacao:
  - CRUD de mantimentos
  - filtro por status
  - edicao de configuracoes institucionais
  - menu hamburguer no mobile
  - salvamento em lote dos mantimentos com botao fixo no rodape
- Sempre que houver alteracao administrativa:
  - `Cache::flush()`
  - atualizacao de timestamp global (`configuracoes.updated_at`)

## Estrutura de dados

### `mantimentos`

- `id`
- `nome` (string)
- `status` enum: `vermelho`, `amarelo`, `abastecido`
- `created_at`
- `updated_at`
- indice em `status`

### `configuracoes`

- `id`
- `nome_paroquia`
- `texto_home`
- `chave_pix`
- `endereco`
- `google_maps_link`
- `created_at`
- `updated_at` (controle global de ultima atualizacao)

## Ambiente local

1. Instale dependencias:

```bash
composer install
```

2. Configure `.env` (ou copie de `.env.example`).

3. Gere chave:

```bash
php artisan key:generate
```

4. Rode migrations e seeders:

```bash
php artisan migrate --seed
```

5. Inicie o servidor:

```bash
php artisan serve
```

## Credencial inicial de admin

Vem via `.env`:

- `ADMIN_EMAIL=admin@santabernadete.bymovve.com`
- `ADMIN_PASSWORD=admin123456`

Altere antes de producao.

## Deploy no EasyPanel com Dockerfile

O repositorio ja possui:

- `Dockerfile`
- `.dockerignore`
- `docker/entrypoint.sh`

### Passos

1. Suba o codigo no GitHub.
2. No EasyPanel, crie o app a partir do repo e selecione build por `Dockerfile`.
3. Crie/associe um banco MySQL no EasyPanel.
4. Configure variaveis de ambiente no app:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://santabernadete.bymovve.com`
   - `APP_KEY=...` (ou use `APP_KEY_GENERATE=true` no primeiro start)
   - `DB_CONNECTION=mysql`
   - `DB_HOST=...`
   - `DB_PORT=3306`
   - `DB_DATABASE=...`
   - `DB_USERNAME=...`
   - `DB_PASSWORD=...`
   - `ADMIN_EMAIL=...`
   - `ADMIN_PASSWORD=...`
5. Para primeira inicializacao, opcional:
   - `RUN_MIGRATIONS=true`
   - `RUN_SEEDER=true`
6. Publique o dominio e ative SSL no EasyPanel.

Depois do primeiro start, desative `RUN_MIGRATIONS` e `RUN_SEEDER`.

## Testes

```bash
php artisan test
```
