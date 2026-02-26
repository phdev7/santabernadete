<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use Illuminate\Http\JsonResponse;

class AdminSyncController extends Controller
{
    public function state(): JsonResponse
    {
        $configuracao = Configuracao::singleton();

        return response()->json([
            'sync_version' => (int) ($configuracao->sync_version ?? 1),
            'updated_at' => optional($configuracao->updated_at)->toIso8601String(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
