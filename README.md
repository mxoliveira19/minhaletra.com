# Minha Letra

Site PHP com Docker, MariaDB e Vite.

## Isolamento Docker local

Os containers nao montam mais a raiz inteira do projeto em `/var/www/html`.

- `minhaletra_php` usa a imagem gerada pelo `Dockerfile` e recebe somente o codigo necessario para runtime: `app/`, `public/`, `database/schema.sql`, `database/migrations/` e `scripts/`.
- `minhaletra_nginx` usa uma imagem propria baseada em `nginx:stable-alpine` e enxerga somente `/var/www/html/public`.
- `docker/nginx/default.conf` continua montado no nginx em modo somente leitura.
- Dumps locais, `meus_docs/`, `.git/`, `.env`, backups, documentacao local e relatorios nao devem aparecer dentro dos containers.
- `.dockerignore` protege o contexto da imagem contra dumps e arquivos locais sensiveis.

Como o codigo entra pelas imagens, alteracoes em PHP, views, assets ou configuracao exigem rebuild.

## Desenvolvimento local

Suba o ambiente local sem remover volumes:

```bash
docker compose up -d --build
```

Depois de alterar codigo PHP, views, `src/`, `public/`, migrations ou configuracao do nginx, reconstrua:

```bash
docker compose build minhaletra_php minhaletra_nginx
docker compose up -d minhaletra_php minhaletra_nginx
```

Para parar mantendo banco e volumes:

```bash
docker compose stop
```

Nao use `docker compose down -v` em ambientes com dados reais.

## Assets Vite

O build dos assets roda dentro do `Dockerfile`:

```bash
npm ci
npm run build
```

Os arquivos finais ficam em `public/build/` dentro das imagens. O nginx serve esses arquivos a partir de `/var/www/html/public/build`.

Para gerar assets no workspace local, use:

```bash
npm ci
npm run build
```

## Banco de dados

Os dados reais ficam no volume MariaDB configurado pelo Docker Compose. Dumps, backups e bancos locais nao devem entrar no Git nem na imagem Docker.

Configure o banco por variaveis de ambiente. Use `.env.example` como referencia e mantenha senhas somente em `.env` local ou no ambiente de producao.

As tabelas obrigatorias para o funcionamento atual sao `textos` e `usuarios`. A tabela `poems` esta versionada em `database/schema.sql` e em `database/migrations/003_create_poems.sql`, mas permanece opcional enquanto a funcionalidade correspondente nao for adotada. A producao atual pode funcionar sem `poems`; quando essa estrutura for necessaria, aplique a migration 003 de forma explicita e controlada.

`DB_AUTO_INIT_SCHEMA` deve ficar `0` por padrao. Em um ambiente novo, defina `DB_AUTO_INIT_SCHEMA=1` apenas para uma primeira inicializacao controlada do `database/schema.sql`, ou execute as migrations explicitamente:

```bash
docker compose run --rm --no-deps minhaletra_php php scripts/migrate.php
```

Para criar um administrador, use o script CLI e forneca credenciais por variaveis de ambiente ou argumentos. O script nao imprime a senha e nao sobrescreve usuarios existentes sem `--update`:

```bash
docker compose run --rm --no-deps minhaletra_php php scripts/create_admin.php --email admin@example.com --name "Admin"
```

Seeds de desenvolvimento devem ser manuais. Producao nunca deve importar dump local. Antes de qualquer alteracao estrutural, faca backup do banco.

Migrations nunca sao executadas automaticamente durante requisicoes da aplicacao.
