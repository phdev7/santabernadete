<?php

namespace App\Services;

use App\Models\Configuracao;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminUpdateService
{
    public function refreshState(): void
    {
        Cache::flush();

        $configuracao = Configuracao::singleton();

        DB::table('configuracoes')
            ->where('id', $configuracao->id)
            ->update([
                'sync_version' => DB::raw('sync_version + 1'),
                'updated_at' => now(),
            ]);
    }
}
