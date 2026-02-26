<?php

namespace App\Services;

use App\Models\Configuracao;
use Illuminate\Support\Facades\Cache;

class AdminUpdateService
{
    public function refreshState(): void
    {
        Cache::flush();

        $configuracao = Configuracao::singleton();
        $configuracao->touch();
    }
}
