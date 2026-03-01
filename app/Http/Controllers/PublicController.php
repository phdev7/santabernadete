<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\Mantimento;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    public function home(): View
    {
        /** @var array{configuracao: Configuracao, mantimentos: \Illuminate\Support\Collection<int, Mantimento>} $data */
        $data = Cache::remember('public.home', now()->addMinutes(10), function (): array {
            return [
                'configuracao' => Configuracao::singleton(),
                'mantimentos' => Mantimento::query()->orderedByPriority()->get(),
            ];
        });

        $ultimaAtualizacaoMinutos = $data['configuracao']->updated_at
            ? max(0, (int) floor($data['configuracao']->updated_at->diffInMinutes(now())))
            : 0;

        return view('public.home', [
            'configuracao' => $data['configuracao'],
            'mantimentos' => $data['mantimentos'],
            'ultimaAtualizacaoMinutos' => $ultimaAtualizacaoMinutos,
            'statusLabels' => Mantimento::statusLabels(),
        ]);
    }

    public function donate(): View
    {
        /** @var Configuracao $configuracao */
        $configuracao = Cache::remember('public.donate', now()->addMinutes(10), function (): Configuracao {
            return Configuracao::singleton();
        });

        return view('public.doar', [
            'configuracao' => $configuracao,
        ]);
    }
}
