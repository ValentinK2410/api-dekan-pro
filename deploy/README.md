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
