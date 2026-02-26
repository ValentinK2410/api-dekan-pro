# Быстрый деплой игрового сервера

## Шаг 1. Сборка в Unity (на Mac/PC)

1. **Unity Hub** → Add Modules → установить **Linux Build Support (Mono)**
2. Открыть проект MultiplayerPersona
3. Меню **Build → Build Dedicated Server (Linux, для api.dekan.pro)**
4. Результат: `Builds/LinuxServer/MultiplayerPersonaServer.x86_64` + папка `_Data`

---

## Шаг 2. Создать папку на сервере и скопировать билд

**На сервере (SSH root@82.146.39.18):**

```bash
mkdir -p /var/www/www-root/data/www/api.dekan.pro/game-server
chown -R www-root:www-root /var/www/www-root/data/www/api.dekan.pro/game-server
```

**С локального Mac (из папки Unity-проекта MultiplayerPersona):**

```bash
cd /путь/к/MultiplayerPersona   # или 2026/MultiplayerPersona
scp -r Builds/LinuxServer/* root@82.146.39.18:/var/www/www-root/data/www/api.dekan.pro/game-server/
```

---

## Шаг 3. Установка и запуск на сервере

```bash
ssh root@82.146.39.18
cd /var/www/www-root/data/www/api.dekan.pro
sudo bash deploy/setup-game-server.sh
```

---

## Шаг 4. Порт (если не открыт)

```bash
sudo ufw allow 7778/udp
sudo ufw allow 7778/tcp
sudo ufw reload
```

---

## Проверка

```bash
sudo systemctl status game-server-api-dekan
sudo journalctl -u game-server-api-dekan -f
```

Игроки подключаются кнопкой **Играть** к `api.dekan.pro`.
