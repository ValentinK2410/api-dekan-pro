#!/bin/bash
# Диагностика 404 на /api/* — запускать на сервере
APP_DIR="${1:-/var/www/www-root/data/www/api.dekan.pro}"
cd "$APP_DIR" || exit 1

echo "=== Проверка API routes api.dekan.pro ==="
echo ""

echo "1. Файлы routes и bootstrap:"
ls -la routes/api.php bootstrap/app.php 2>/dev/null || echo "  ФАЙЛЫ НЕ НАЙДЕНЫ"
echo ""

echo "2. Очистка кэша..."
php artisan config:clear 2>/dev/null
php artisan route:clear 2>/dev/null
echo ""

echo "3. Зарегистрированные API маршруты:"
php artisan route:list --path=api 2>/dev/null || php artisan route:list | grep -E "api/auth|api/progress" || echo "  API-маршруты не найдены"
echo ""

echo "4. Тест локально (если запущен php -S):"
echo "   curl -X POST http://127.0.0.1:8000/api/auth/register -H 'Content-Type: application/json' -d '{\"name\":\"T\",\"email\":\"t@t.com\",\"password\":\"p\"}'"
echo ""
echo "Готово. Если route:list показывает api/auth/register — маршруты есть."
echo "404 = либо Nginx не отдаёт /api в Laravel, либо на сервере старая версия кода."
