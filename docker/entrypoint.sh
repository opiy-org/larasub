#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

echo "[entrypoint] Ожидание БД db:3306..."
until (echo > /dev/tcp/db/3306) >/dev/null 2>&1; do
  sleep 1
done
echo "[entrypoint] БД доступна."


echo "[entrypoint] Запуск php-fpm..."
exec "$@"
