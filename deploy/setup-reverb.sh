#!/bin/bash
# Скрипт установки и запуска Reverb на сервере
# Запуск: sudo bash setup-reverb.sh

set -e

APP_DIR="/var/www/www-root/data/www/api.dekan.pro"
SERVICE_NAME="reverb-api-dekan"

echo "=== Установка Reverb для api.dekan.pro ==="

# 1. Остановить старый процесс если есть
pkill -f "reverb:start" 2>/dev/null || true
sleep 2

# 2. Проверить порт 8081
if lsof -i :8081 2>/dev/null | grep -q LISTEN; then
    echo "Порт 8081 занят. Освободите его или измените REVERB_SERVER_PORT."
    exit 1
fi

# 3. Установить Reverb через systemd
echo "Создание systemd сервиса..."
cp "$APP_DIR/deploy/reverb.service" /etc/systemd/system/reverb-api-dekan.service

systemctl daemon-reload
systemctl enable reverb-api-dekan
systemctl restart reverb-api-dekan

echo ""
echo "=== Reverb запущен ==="
systemctl status reverb-api-dekan --no-pager

echo ""
echo "Проверка: curl -s http://127.0.0.1:8081/app -o /dev/null -w '%{http_code}'"
curl -s http://127.0.0.1:8081/app -o /dev/null -w "HTTP код: %{http_code}\n" || echo "Reverb ещё запускается..."
