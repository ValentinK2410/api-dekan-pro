# API-мультиплеер (game_sessions)

Игроки входят в общую игру через API api.dekan.pro. Работает с любых устройств, включая мобильные.

## API

### POST /api/game/join
Вход в игру (auth required).

```json
{"player_name": "Игрок", "scene": "Platformer"}
```

### POST /api/game/leave
Выход из игры (auth required).

### GET /api/game/players
Список игроков в игре с позициями (auth required). Исключает текущего пользователя.

```json
{
  "players": [
    {
      "user_id": 2,
      "player_name": "Друг",
      "position": {"x": 1.5, "y": 0, "z": 3},
      "rotation": {"x": 0, "y": 0.7, "z": 0, "w": 0.7},
      "scene": "Platformer",
      "last_seen_at": "2026-02-27T12:00:00Z"
    }
  ]
}
```

Позиция обновляется при каждом `PUT /api/player/position` (если у пользователя есть активная сессия).

## Миграция

```bash
php artisan migrate --force
```

## Unity

1. Войти или зарегистрироваться.
2. Нажать «Играть онлайн» (вместо «Создать игру» или ввода IP).
3. Установить API-мультиплеер: меню **Platformer → Setup API Multiplayer** (один раз для сцены).
4. Запустить игру — другие игроки видны как капсулы с обновлением позиции ~3 раза в секунду.
