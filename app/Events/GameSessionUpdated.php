<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Отправляется при обновлении игровой сессии (позиция, кубы и т.д.).
 * Клиенты подписанные на game.world получают обновления сразу, без ожидания следующего poll.
 */
class GameSessionUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $user_id,
        public string $player_name,
        public ?array $position,
        public ?array $rotation,
        public ?string $scene,
        public ?int $carried_cube_index,
        public ?array $cube_position,
        public ?array $cube_rotation,
        public ?int $focused_cube_index,
        public array $solved_platform_indices,
        public array $platform_contributions,
        public array $collected_collectible_indices,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('game.world'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.updated';
    }
}
