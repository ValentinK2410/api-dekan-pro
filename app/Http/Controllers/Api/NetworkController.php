<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    /**
     * GET /api/network/my-ip — возвращает IP клиента (для показа хосту в игре).
     * Учитывает X-Forwarded-For при работе за reverse proxy.
     */
    public function myIp(Request $request): JsonResponse
    {
        return response()->json(['ip' => $request->ip()]);
    }
}
