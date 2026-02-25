#!/bin/bash
# Создаёт архив для деплоя API — запускать на локальной машине
# Использование: ./deploy/make-deploy-archive.sh
# Затем: scp deploy-api.tar.gz root@сервер:/tmp/ и на сервере: cd /var/www/... && tar -xzf /tmp/deploy-api.tar.gz

cd "$(dirname "$0")/.." || exit 1
OUT=deploy-api.tar.gz

tar -czf "$OUT" \
  routes/api.php \
  bootstrap/app.php \
  app/Http/Controllers/Api/AuthController.php \
  app/Http/Controllers/Api/ProgressController.php \
  app/Http/Middleware/Cors.php \
  app/Models/User.php \
  database/migrations/2026_02_26_000001_create_game_progress_table.php \
  config/sanctum.php 2>/dev/null || true

echo "Создан $OUT"
echo "Загрузите на сервер и распакуйте:"
echo "  scp $OUT root@сервер:/tmp/"
echo "  ssh root@сервер 'cd /var/www/www-root/data/www/api.dekan.pro && tar -xzf /tmp/$OUT'"
echo "  ssh root@сервер 'cd /var/www/www-root/data/www/api.dekan.pro && composer install --no-dev && php artisan migrate --force && php artisan config:clear && php artisan route:clear'"
