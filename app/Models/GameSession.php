<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameSession extends Model
{
    protected $table = 'game_sessions';

    protected $fillable = [
        'user_id',
        'player_name',
        'position',
        'rotation',
        'scene',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'array',
            'rotation' => 'array',
            'last_seen_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Игрок считается активным, если был виден в последние 60 секунд */
    public static function staleSeconds(): int
    {
        return 60;
    }

    public function isStale(): bool
    {
        return $this->last_seen_at->addSeconds(self::staleSeconds())->isPast();
    }
}
