<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameProgress extends Model
{
    protected $table = 'game_progress';

    protected $fillable = [
        'user_id',
        'coins',
        'extra',
        'last_save_at',
    ];

    protected function casts(): array
    {
        return [
            'coins' => 'float',
            'extra' => 'array',
            'last_save_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
