# Деплой api.dekan.pro

## Запуск Reverb (WebSocket)

### Вариант 1: systemd (рекомендуется)

```bash
cd /var/www/www-root/data/www/api.dekan.pro
sudo cp deploy/reverb.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable reverb-api-dekan
sudo systemctl start reverb-api-dekan
sudo systemctl status reverb-api-dekan
```

### Вариант 2: Скрипт

```bash
cd /var/www/www-root/data/www/api.dekan.pro
sudo bash deploy/setup-reverb.sh
```

### Вариант 3: nohup (временно)

```bash
cd /var/www/www-root/data/www/api.dekan.pro
nohup php artisan reverb:start --host=0.0.0.0 --port=8081 >> /var/log/reverb.log 2>&1 &
```

## Проверка

- WebSocket: wss://api.dekan.pro/app
- Лог: `journalctl -u reverb-api-dekan -f`

---

## Игровой сервер (MultiplayerPersona)

Работает в той же папке api.dekan.pro, порт 7778.

1. Собрать в Unity: **Build → Build Dedicated Server (Linux)**
2. Скопировать: `scp -r Builds/LinuxServer/* user@api.dekan.pro:/var/www/www-root/data/www/api.dekan.pro/game-server/`
3. Запустить: `sudo bash deploy/setup-game-server.sh`
4. Открыть порт: `sudo ufw allow 7778/udp && sudo ufw allow 7778/tcp`

Подробнее: [DEPLOY_GAME_SERVER.md](DEPLOY_GAME_SERVER.md)

---

## Устранение 404 на `/api/*`

Если `POST /api/auth/register` возвращает 404:

### 1. Проверить наличие файлов

```bash
cd /var/www/www-root/data/www/api.dekan.pro
ls -la routes/api.php bootstrap/app.php
```

### 2. Очистить кэш и проверить маршруты

```bash
php artisan config:clear
php artisan route:clear
php artisan route:list
```

Убедитесь, что в списке есть `POST api/auth/register`, `POST api/auth/login` и т.д.

### 3. Nginx — убедиться, что Laravel получает запросы

В блоке `location /` (или `server`) для api.dekan.pro должна быть директива:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

И секция `location ~ \.php$` для FastCGI. Без этого запросы к `/api/auth/register` не попадают в Laravel.

### 4. Деплой свежего кода

```bash
cd /var/www/www-root/data/www/api.dekan.pro
git pull  # или залить файлы вручную
composer install --no-dev
php artisan migrate --force
php artisan config:clear
php artisan route:clear
```
