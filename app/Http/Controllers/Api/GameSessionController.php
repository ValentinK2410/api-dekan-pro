<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameSessionController extends Controller
{
    /**
     * POST /api/game/join — войти в игру (зарегистрировать сессию)
     *
     * @bodyParam player_name string required Имя игрока (отображается другим)
     * @bodyParam scene string optional Текущая сцена
     */
    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_name' => ['required', 'string', 'max:64'],
            'scene' => ['sometimes', 'string', 'max:255'],
        ]);

        $user = $request->user();

        $session = GameSession::updateOrCreate(
            ['user_id' => $user->id],
            [
                'player_name' => $validated['player_name'],
                'scene' => $validated['scene'] ?? null,
                'position' => null,
                'rotation' => null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
            'session_id' => $session->id,
        ]);
    }

    /**
     * POST /api/game/leave — выйти из игры
     */
    public function leave(Request $request): JsonResponse
    {
        GameSession::where('user_id', $request->user()->id)->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * GET /api/game/players — список игроков в игре с позициями
     * Исключает текущего пользователя. Устаревшие (>60 сек без обновления) не возвращаются.
     */
    public function players(Request $request): JsonResponse
    {
        $currentUserId = $request->user()->id;
        $stale = now()->subSeconds(GameSession::staleSeconds());

        $players = GameSession::where('user_id', '!=', $currentUserId)
            ->where('last_seen_at', '>=', $stale)
            ->get()
            ->map(fn (GameSession $s) => [
                'user_id' => $s->user_id,
                'player_name' => $s->player_name,
                'position' => $s->position,
                'rotation' => $s->rotation,
                'scene' => $s->scene,
                'last_seen_at' => $s->last_seen_at->toIso8601String(),
            ]);

        return response()->json(['players' => $players]);
    }
}
