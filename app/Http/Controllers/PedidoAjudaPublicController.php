<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\PedidoAjuda;
use App\Services\AdminUpdateService;
use App\Services\PedidoAjudaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PedidoAjudaPublicController extends Controller
{
    public function create(): View
    {
        return view('public.preciso_de_ajuda', [
            'configuracao' => Configuracao::singleton(),
        ]);
    }

    public function store(
        Request $request,
        PedidoAjudaService $pedidoAjudaService,
        AdminUpdateService $updateService
    ): RedirectResponse {
        $data = $request->validate([
            'nome_recebedor' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:32'],
            'endereco_completo_referencias' => ['required', 'string'],
            'itens' => ['required', 'string'],
        ]);

        $pedido = $pedidoAjudaService->createWithControle([
            ...$data,
            'status' => PedidoAjuda::STATUS_EM_ANDAMENTO,
        ]);

        $updateService->refreshState();

        return redirect()
            ->route('pedido.show', ['codigo' => sprintf('%05d', (int) $pedido->numero_sequencial)])
            ->with('status', 'Pedido registrado com sucesso.');
    }

    public function show(string $codigo): View
    {
        $codigoPublico = sprintf('pedido-%05d', (int) $codigo);

        $pedido = PedidoAjuda::query()
            ->with('controleEnvio')
            ->where('codigo_publico', $codigoPublico)
            ->firstOrFail();

        return view('public.pedido_ticket', [
            'configuracao' => Configuracao::singleton(),
            'pedido' => $pedido,
            'statusLabels' => PedidoAjuda::statusLabels(),
        ]);
    }
}
