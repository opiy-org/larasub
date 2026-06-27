#!/usr/bin/env bash

if [ ! -d vendor ]; then
  echo "[entrypoint] Установка зависимостей composer..."
  composer install --no-interaction --prefer-dist --no-progress
else
  echo "[entrypoint] vendor уже существует, пропуск composer install"
fi

echo "[entrypoint] Подготовка workbench (testbench build)..."
php -d memory_limit=-1 vendor/bin/testbench workbench:build --ansi || true

if [ -f workbench/.env.docker ] && [ ! -f workbench/.env ]; then
  echo "[entrypoint] Копирование workbench/.env.docker -> workbench/.env"
  cp workbench/.env.docker workbench/.env
fi

# Генерация APP_KEY через testbench artisan, если возможно
if [ -f vendor/bin/testbench ]; then
  echo "[entrypoint] Генерация APP_KEY (если требуется)..."
  php vendor/bin/testbench artisan key:generate --force || true

  echo "[entrypoint] Выполнение миграций (при необходимости)..."
  php vendor/bin/testbench migrate --force || true
fi
