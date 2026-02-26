<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerPositionController extends Controller
{
    /**
     * GET /api/player/position — получить сохранённую позицию пользователя
     */
    public function show(Request $request): JsonResponse
    {
        $progress = GameProgress::where('user_id', $request->user()->id)->first();
        $extra = $progress?->extra ?? [];

        $position = $extra['position'] ?? null;
        $rotation = $extra['rotation'] ?? null;
        $velocity = $extra['velocity'] ?? null;
        $scene = $extra['scene'] ?? null;

        return response()->json([
            'position' => $position,
            'rotation' => $rotation,
            'velocity' => $velocity,
            'scene' => $scene,
        ]);
    }

    /**
     * PUT /api/player/position — сохранить позицию и движение пользователя
     *
     * @bodyParam position object optional {"x":0,"y":0,"z":0}
     * @bodyParam rotation object optional {"x":0,"y":0,"z":0,"w":1}
     * @bodyParam velocity object optional {"x":0,"y":0,"z":0}
     * @bodyParam scene string optional Имя сцены
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'position' => ['sometimes', 'array'],
            'position.x' => ['sometimes', 'numeric'],
            'position.y' => ['sometimes', 'numeric'],
            'position.z' => ['sometimes', 'numeric'],
            'rotation' => ['sometimes', 'array'],
            'rotation.x' => ['sometimes', 'numeric'],
            'rotation.y' => ['sometimes', 'numeric'],
            'rotation.z' => ['sometimes', 'numeric'],
            'rotation.w' => ['sometimes', 'numeric'],
            'velocity' => ['sometimes', 'array'],
            'velocity.x' => ['sometimes', 'numeric'],
            'velocity.y' => ['sometimes', 'numeric'],
            'velocity.z' => ['sometimes', 'numeric'],
            'scene' => ['sometimes', 'string', 'max:255'],
        ]);

        $progress = GameProgress::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['coins' => 0]
        );

        $extra = $progress->extra ?? [];
        if (isset($validated['position'])) {
            $extra['position'] = $validated['position'];
        }
        if (isset($validated['rotation'])) {
            $extra['rotation'] = $validated['rotation'];
        }
        if (isset($validated['velocity'])) {
            $extra['velocity'] = $validated['velocity'];
        }
        if (isset($validated['scene'])) {
            $extra['scene'] = $validated['scene'];
        }

        $progress->update([
            'extra' => $extra,
            'last_save_at' => now(),
        ]);

        return response()->json([
            'position' => $extra['position'] ?? null,
            'rotation' => $extra['rotation'] ?? null,
            'velocity' => $extra['velocity'] ?? null,
            'scene' => $extra['scene'] ?? null,
        ]);
    }
}
