<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameProgress;
use App\Models\GameSession;
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
            'carried_cube_index' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:255'],
            'cube_position' => ['sometimes', 'array'],
            'cube_position.x' => ['sometimes', 'numeric'],
            'cube_position.y' => ['sometimes', 'numeric'],
            'cube_position.z' => ['sometimes', 'numeric'],
            'cube_rotation' => ['sometimes', 'array'],
            'cube_rotation.x' => ['sometimes', 'numeric'],
            'cube_rotation.y' => ['sometimes', 'numeric'],
            'cube_rotation.z' => ['sometimes', 'numeric'],
            'cube_rotation.w' => ['sometimes', 'numeric'],
            'focused_cube_index' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:255'],
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

        // Обновить позицию и состояние кубов в игровой сессии (для мультиплеера через API)
        $session = GameSession::where('user_id', $request->user()->id)->first();
        if ($session) {
            $update = [
                'position' => $extra['position'] ?? $session->position,
                'rotation' => $extra['rotation'] ?? $session->rotation,
                'scene' => $extra['scene'] ?? $session->scene,
                'last_seen_at' => now(),
            ];
            if (array_key_exists('carried_cube_index', $validated)) {
                $carried = $validated['carried_cube_index'] ?? null;
                $update['carried_cube_index'] = $carried;
                if ($carried === null) {
                    $update['cube_position'] = null;
                    $update['cube_rotation'] = null;
                }
            }
            if (isset($validated['cube_position'])) {
                $update['cube_position'] = $validated['cube_position'];
            }
            if (isset($validated['cube_rotation'])) {
                $update['cube_rotation'] = $validated['cube_rotation'];
            }
            if (array_key_exists('focused_cube_index', $validated)) {
                $update['focused_cube_index'] = $validated['focused_cube_index'] ?? null;
            }
            $session->update($update);
        }

        return response()->json([
            'position' => $extra['position'] ?? null,
            'rotation' => $extra['rotation'] ?? null,
            'velocity' => $extra['velocity'] ?? null,
            'scene' => $extra['scene'] ?? null,
        ]);
    }
}
