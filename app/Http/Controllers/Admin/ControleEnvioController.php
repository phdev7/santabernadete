<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Models\PedidoAjuda;
use App\Services\AdminUpdateService;
use App\Services\PedidoAjudaService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ControleEnvioController extends Controller
{
    public function index(Request $request): View
    {
        $statusFiltro = $request->query('status');
        $periodoFiltro = $request->query('periodo');
        $busca = trim((string) $request->query('busca', ''));

        if (! in_array($statusFiltro, PedidoAjuda::STATUS_ORDER, true)) {
            $statusFiltro = null;
        }

        if (! in_array($periodoFiltro, ['hoje', '7dias', '30dias'], true)) {
            $periodoFiltro = null;
        }

        $query = PedidoAjuda::query()
            ->with('controleEnvio')
            ->orderedByStatus();

        if ($statusFiltro !== null) {
            $query->where('status', $statusFiltro);
        }

        $this->applyPeriodoFiltro($query, $periodoFiltro);
        $this->applyBusca($query, $busca);

        $configuracao = Configuracao::singleton();

        return view('admin.controle_envios.index', [
            'configuracao' => $configuracao,
            'syncVersion' => (int) ($configuracao->sync_version ?? 1),
            'pedidos' => $query->get(),
            'statusLabels' => PedidoAjuda::statusLabels(),
            'statusFiltro' => $statusFiltro,
            'periodoFiltro' => $periodoFiltro,
            'busca' => $busca,
            'kpis' => [
                'em_andamento' => PedidoAjuda::query()->where('status', PedidoAjuda::STATUS_EM_ANDAMENTO)->count(),
                'feito' => PedidoAjuda::query()->where('status', PedidoAjuda::STATUS_FEITO)->count(),
                'total_hoje' => PedidoAjuda::query()->whereDate('created_at', now()->toDateString())->count(),
                'total_7_dias' => PedidoAjuda::query()->where('created_at', '>=', now()->subDays(7)->startOfDay())->count(),
            ],
            'recorrentes' => $this->recorrentes(),
        ]);
    }

    public function store(
        Request $request,
        PedidoAjudaService $pedidoAjudaService,
        AdminUpdateService $updateService
    ): RedirectResponse {
        $data = $request->validate([
            'nome_recebedor' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:20'],
            'telefone' => ['required', 'string', 'max:32'],
            'endereco_completo_referencias' => ['required', 'string'],
            'itens' => ['required', 'string'],
            'nome_entregador' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
            'status' => ['required', Rule::in(PedidoAjuda::STATUS_ORDER)],
        ]);

        $pedidoAjudaService->createWithControle($data);
        $updateService->refreshState();

        return back()->with('status', 'Pedido cadastrado com sucesso.');
    }

    public function bulkUpdate(Request $request, AdminUpdateService $updateService): RedirectResponse
    {
        $data = $request->validate([
            'envios' => ['required', 'array', 'min:1'],
            'envios.*.id' => ['required', 'integer', 'exists:pedidos_ajuda,id'],
            'envios.*.nome_recebedor' => ['required', 'string', 'max:255'],
            'envios.*.cpf' => ['required', 'string', 'max:20'],
            'envios.*.telefone' => ['required', 'string', 'max:32'],
            'envios.*.endereco_completo_referencias' => ['required', 'string'],
            'envios.*.itens' => ['required', 'string'],
            'envios.*.nome_entregador' => ['nullable', 'string', 'max:255'],
            'envios.*.notas' => ['nullable', 'string'],
            'envios.*.status' => ['required', Rule::in(PedidoAjuda::STATUS_ORDER)],
        ]);

        DB::transaction(function () use ($data): void {
            foreach ($data['envios'] as $envioData) {
                $pedido = PedidoAjuda::query()->with('controleEnvio')->findOrFail($envioData['id']);

                $pedido->update([
                    'nome_recebedor' => $envioData['nome_recebedor'],
                    'cpf' => $envioData['cpf'],
                    'telefone' => $envioData['telefone'],
                    'endereco_completo_referencias' => $envioData['endereco_completo_referencias'],
                    'itens' => $envioData['itens'],
                    'status' => $envioData['status'],
                ]);

                if ($pedido->controleEnvio) {
                    $pedido->controleEnvio->update([
                        'nome_entregador' => $envioData['nome_entregador'] ?: null,
                        'notas' => $envioData['notas'] ?: null,
                    ]);
                } else {
                    $pedido->controleEnvio()->create([
                        'nome_entregador' => $envioData['nome_entregador'] ?: null,
                        'notas' => $envioData['notas'] ?: null,
                    ]);
                }
            }
        });

        $updateService->refreshState();

        return back()->with('status', 'Alteracoes do controle de envios salvas com sucesso.');
    }

    public function destroy(PedidoAjuda $pedido, AdminUpdateService $updateService): RedirectResponse
    {
        $pedido->delete();
        $updateService->refreshState();

        return back()->with('status', 'Pedido removido com sucesso.');
    }

    private function applyPeriodoFiltro(Builder $query, ?string $periodoFiltro): void
    {
        if ($periodoFiltro === 'hoje') {
            $query->whereDate('created_at', now()->toDateString());

            return;
        }

        if ($periodoFiltro === '7dias') {
            $query->where('created_at', '>=', now()->subDays(7)->startOfDay());

            return;
        }

        if ($periodoFiltro === '30dias') {
            $query->where('created_at', '>=', now()->subDays(30)->startOfDay());
        }
    }

    private function applyBusca(Builder $query, string $busca): void
    {
        if ($busca === '') {
            return;
        }

        $telefoneBusca = PedidoAjuda::normalizePhone($busca);

        $cpfBusca = PedidoAjuda::normalizeCpf($busca);

        $query->where(function (Builder $builder) use ($busca, $telefoneBusca, $cpfBusca): void {
            $builder
                ->where('nome_recebedor', 'like', '%'.$busca.'%')
                ->orWhere('telefone', 'like', '%'.$busca.'%')
                ->orWhere('cpf', 'like', '%'.$busca.'%');

            if ($telefoneBusca !== '') {
                $builder->orWhere('telefone_normalizado', 'like', '%'.$telefoneBusca.'%');
            }

            if ($cpfBusca !== '') {
                $builder->orWhere('cpf_normalizado', 'like', '%'.$cpfBusca.'%');
            }
        });
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *     nome_recebedor: string,
     *     telefone: string,
     *     quantidade_pedidos: int,
     *     ultima_solicitacao: Carbon,
     *     intervalo_dias: int
     * }>
     */
    private function recorrentes(): Collection
    {
        $dados = PedidoAjuda::query()
            ->selectRaw(
                'nome_normalizado, endereco_normalizado, MAX(nome_recebedor) as nome_recebedor, MAX(telefone) as telefone, COUNT(*) as quantidade_pedidos, MAX(created_at) as ultima_solicitacao'
            )
            ->whereNotNull('nome_normalizado')
            ->where('nome_normalizado', '!=', '')
            ->whereNotNull('endereco_normalizado')
            ->where('endereco_normalizado', '!=', '')
            ->groupBy('nome_normalizado', 'endereco_normalizado')
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('quantidade_pedidos')
            ->orderByDesc('ultima_solicitacao')
            ->limit(10)
            ->get();

        return $dados->map(function ($item) {
            $ultimaSolicitacao = Carbon::parse($item->ultima_solicitacao);

            return [
                'nome_recebedor' => (string) $item->nome_recebedor,
                'telefone' => (string) $item->telefone,
                'quantidade_pedidos' => (int) $item->quantidade_pedidos,
                'ultima_solicitacao' => $ultimaSolicitacao,
                'intervalo_dias' => $ultimaSolicitacao->diffInDays(now()),
            ];
        });
    }
}
