<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * GET /api/progress — получить прогресс текущего пользователя
     */
    public function show(Request $request): JsonResponse
    {
        $progress = GameProgress::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['coins' => 0]
        );

        return response()->json([
            'coins' => (float) $progress->coins,
            'last_save_utc' => $progress->last_save_at?->toIso8601String() ?? '',
            'extra' => $progress->extra ?? [],
        ]);
    }

    /**
     * PUT /api/progress — сохранить прогресс
     *
     * @bodyParam coins number required Монеты. Example: 15.5
     * @bodyParam extra object optional Доп. данные. Example: {"level":2}
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'coins' => ['required', 'numeric', 'min:0'],
            'extra' => ['sometimes', 'array'],
        ]);

        $progress = GameProgress::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'coins' => (float) $validated['coins'],
                'extra' => $validated['extra'] ?? null,
                'last_save_at' => now(),
            ]
        );

        return response()->json([
            'coins' => (float) $progress->coins,
            'last_save_utc' => $progress->last_save_at->toIso8601String(),
        ]);
    }
}
