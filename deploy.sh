#!/usr/bin/env sh
set -eu

PROJECT_DIR="/opt/docker/sites/minhaletra.com"
BRANCH="${DEPLOY_BRANCH:-main}"
PHP_SERVICE="${PHP_SERVICE:-minhaletra_php}"
BUILD_ASSETS="${BUILD_ASSETS:-1}"

cd "$PROJECT_DIR"

if [ ! -d .git ] || [ ! -f docker-compose.yml ]; then
echo "Erro: $PROJECT_DIR nao parece ser o projeto minhaletra.com." >&2
exit 1
fi

current_branch="$(git branch --show-current)"
echo "Projeto: $(pwd)"
echo "Branch atual: ${current_branch:-DETACHED}"

if [ "$current_branch" != "$BRANCH" ]; then
echo "Erro: branch atual '$current_branch', esperado '$BRANCH'." >&2
exit 1
fi

if ! git diff --quiet || ! git diff --cached --quiet; then
echo "Erro: existem alteracoes locais rastreadas. Revise antes do deploy." >&2
git status -sb
exit 1
fi

untracked="$(git ls-files --others --exclude-standard | grep -v '^deploy.sh$' || true)"
if [ -n "$untracked" ]; then
echo "Erro: existem arquivos nao rastreados. Revise antes do deploy." >&2
git status -sb
exit 1
fi

echo "Buscando atualizacoes..."
git fetch origin
git pull --ff-only origin "$BRANCH"

if [ "$BUILD_ASSETS" = "1" ] && [ -f package.json ]; then
echo "Instalando dependencias e gerando assets Vite dentro do container..."
docker compose run --rm --no-deps "$PHP_SERVICE" sh -lc 'npm ci && npm run build'

if [ -d public/build ]; then
  uid="$(id -u)"
  gid="$(id -g)"

  docker compose run --rm --no-deps "$PHP_SERVICE" \
    chown -R "$uid:$gid" /var/www/html/public/build
fi
fi

echo "Reconstruindo containers do site sem remover volumes..."
mkdir -p storage
touch storage/joinhas.json
docker compose up -d --build

echo "Garantindo permissao de escrita para o log de joinhas..."
docker compose exec -T "$PHP_SERVICE" sh -lc 'mkdir -p /var/www/html/storage && touch /var/www/html/storage/joinhas.json && chown -R www-data:www-data /var/www/html/storage'

echo "Aplicando migracoes do banco..."
docker compose exec -T "$PHP_SERVICE" php scripts/migrate.php

echo "Recriando o Nginx para carregar configuracoes atualizadas..."
docker compose up -d --force-recreate minhaletra_nginx

echo "Estado final:"
git status -sb
git log -1 --oneline --decorate
docker compose ps
