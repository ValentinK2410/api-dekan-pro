#!/bin/bash
# Установка и запуск игрового сервера MultiplayerPersona
# Запуск: sudo bash deploy/setup-game-server.sh
# Требуется: сначала скопировать Builds/LinuxServer/* в game-server/

set -e

APP_DIR="/var/www/www-root/data/www/api.dekan.pro"
GAME_DIR="$APP_DIR/game-server"
SERVICE_NAME="game-server-api-dekan"

echo "=== Установка игрового сервера api.dekan.pro ==="

# 1. Создать директорию
mkdir -p "$GAME_DIR"
chown -R www-root:www-root "$GAME_DIR"

# 2. Проверить наличие бинарника
if [ ! -f "$GAME_DIR/MultiplayerPersonaServer.x86_64" ]; then
    echo "ОШИБКА: MultiplayerPersonaServer.x86_64 не найден в $GAME_DIR"
    echo "Скопируйте файлы из Unity Builds/LinuxServer/:"
    echo "  scp -r Builds/LinuxServer/* user@api.dekan.pro:$GAME_DIR/"
    exit 1
fi

chmod +x "$GAME_DIR/MultiplayerPersonaServer.x86_64"

# 3. Установить systemd
cp "$APP_DIR/deploy/game-server.service" /etc/systemd/system/$SERVICE_NAME.service
systemctl daemon-reload
systemctl enable $SERVICE_NAME
systemctl restart $SERVICE_NAME

echo ""
echo "=== Игровой сервер запущен ==="
systemctl status $SERVICE_NAME --no-pager

echo ""
echo "Порт 7778 должен быть открыт: sudo ufw allow 7778/udp && sudo ufw allow 7778/tcp && sudo ufw reload"
