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
                'carried_cube_index' => $s->carried_cube_index,
                'cube_position' => $s->cube_position,
                'cube_rotation' => $s->cube_rotation,
                'focused_cube_index' => $s->focused_cube_index,
                'solved_platform_indices' => $s->solved_platform_indices ?? [],
                'last_seen_at' => $s->last_seen_at->toIso8601String(),
            ]);

        // Объединение решённых площадок всех игроков (включая текущего) для синхронизации уровня
        $currentSession = GameSession::where('user_id', $currentUserId)->first();
        $allSessions = GameSession::where('last_seen_at', '>=', $stale)->get();
        $solvedUnion = [];
        foreach ($allSessions as $s) {
            $arr = is_array($s->solved_platform_indices) ? $s->solved_platform_indices : [];
            $solvedUnion = array_merge($solvedUnion, $arr);
        }
        $solvedUnion = array_values(array_unique($solvedUnion));

        // Сумма на площадках = объединение вкладов всех игроков (игрок 1 положил 3, игрок 2 — 7 → сумма 10)
        $platformSums = [];
        foreach ($allSessions as $s) {
            $contrib = is_array($s->platform_contributions) ? $s->platform_contributions : [];
            foreach ($contrib as $platformIdx => $value) {
                $k = (string) $platformIdx;
                $platformSums[$k] = ($platformSums[$k] ?? 0) + (int) $value;
            }
        }

        $collectedUnion = [];
        foreach ($allSessions as $s) {
            $arr = is_array($s->collected_collectible_indices) ? $s->collected_collectible_indices : [];
            $collectedUnion = array_merge($collectedUnion, $arr);
        }
        $collectedUnion = array_values(array_unique($collectedUnion));

        return response()->json([
            'players' => $players,
            'solved_platform_indices' => $solvedUnion,
            'platform_sums' => $platformSums,
            'collected_collectible_indices' => $collectedUnion,
        ]);
    }
}
