# Развёртывание игрового сервера MultiplayerPersona

Игра работает по **интернету** через api.dekan.pro. Игроки вводят логин/пароль и нажимают «Играть» — подключаются к серверу.

## 0. Установка Linux Build Support (обязательно)

Без этого сборка для Linux выдаст "build target was unsupported":

1. Откройте **Unity Hub**
2. Выберите нужную версию Unity → **Add Modules** (или ⋮ → Add Modules)
3. Отметьте **Linux Build Support (Mono)**
4. Установите

## 1. Сборка в Unity

1. Откройте проект MultiplayerPersona в Unity.
2. Меню: **Build → Build Dedicated Server (Linux, для интернета)**
3. Сборка появится в `Builds/LinuxServer/`:
   - `MultiplayerPersonaServer.x86_64` — исполняемый файл
   - `MultiplayerPersonaServer_Data/` — папка с данными

## 2. Копирование на сервер

Файлы размещаются **внутри api.dekan.pro** (рядом с Laravel и Reverb):

```bash
# С локального Mac/PC (из папки Unity-проекта)
GAME_DIR="/var/www/www-root/data/www/api.dekan.pro/game-server"
scp -r Builds/LinuxServer/* user@api.dekan.pro:$GAME_DIR/
```

Или rsync:
```bash
rsync -avz Builds/LinuxServer/ user@api.dekan.pro:/var/www/www-root/data/www/api.dekan.pro/game-server/
```

## 3. Настройка на сервере

На сервере api.dekan.pro (в папке проекта):

```bash
cd /var/www/www-root/data/www/api.dekan.pro
sudo bash deploy/setup-game-server.sh
```

Скрипт создаёт папку game-server, ставит systemd-сервис и запускает игру.

## 4. Открыть порт

```bash
# UFW
sudo ufw allow 7778/udp
sudo ufw allow 7778/tcp
sudo ufw reload
```

## 5. Проверка

Игроки подключаются к `api.dekan.pro` (хост из GameServerConfig). Клиент попробует порты 7778, 7779, ... до 7787.

## Важно

- Сервер и Laravel API могут работать на одном хосте (api.dekan.pro).
- Игровой сервер слушает UDP/TCP порт 7778.
- Для работы нужны библиотеки: на Ubuntu/Debian может потребоваться `lib64gcc1` и зависимости Unity.

## Зависимости Linux

Если сервер не запускается:
```bash
# Ubuntu/Debian
sudo apt install libxcursor1 libxrender1 libxi6 libxrandr2 libxss1 libgl1-mesa-glx

# Или полный набор для Unity Linux
sudo apt install libgtk-3-0 libnotify4 libnss3 libxss1 libasound2 libxtst6 xauth
```
