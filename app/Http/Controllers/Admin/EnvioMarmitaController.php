<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Models\EnvioMarmita;
use App\Services\AdminUpdateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EnvioMarmitaController extends Controller
{
    public function index(Request $request): View
    {
        $statusFiltro = $request->query('status');

        $query = EnvioMarmita::query()->orderedByStatus();

        if (in_array($statusFiltro, EnvioMarmita::STATUS_ORDER, true)) {
            $query->where('status', $statusFiltro);
        } else {
            $statusFiltro = null;
        }

        $configuracao = Configuracao::singleton();

        $resumo = [
            'em_andamento' => EnvioMarmita::query()->where('status', EnvioMarmita::STATUS_EM_ANDAMENTO)->count(),
            'feito' => EnvioMarmita::query()->where('status', EnvioMarmita::STATUS_FEITO)->count(),
            'marmitas_pendentes' => (int) EnvioMarmita::query()
                ->where('status', EnvioMarmita::STATUS_EM_ANDAMENTO)
                ->sum('quantidade_marmitas'),
            'agua_pendente' => (int) EnvioMarmita::query()
                ->where('status', EnvioMarmita::STATUS_EM_ANDAMENTO)
                ->sum('quantidade_agua'),
        ];

        return view('admin.envios_marmitas.index', [
            'configuracao' => $configuracao,
            'syncVersion' => (int) ($configuracao->sync_version ?? 1),
            'envios' => $query->get(),
            'statusLabels' => EnvioMarmita::statusLabels(),
            'statusFiltro' => $statusFiltro,
            'resumo' => $resumo,
        ]);
    }

    public function store(Request $request, AdminUpdateService $updateService): RedirectResponse
    {
        $data = $request->validate([
            'quantidade_marmitas' => ['required', 'integer', 'min:1'],
            'quantidade_agua' => ['required', 'integer', 'min:0'],
            'endereco' => ['required', 'string'],
            'notas' => ['nullable', 'string'],
            'status' => ['required', Rule::in(EnvioMarmita::STATUS_ORDER)],
        ]);

        EnvioMarmita::query()->create($data);
        $updateService->refreshState();

        return back()->with('status', 'Envio de marmitas cadastrado com sucesso.');
    }

    public function bulkUpdate(Request $request, AdminUpdateService $updateService): RedirectResponse
    {
        $data = $request->validate([
            'envios' => ['required', 'array', 'min:1'],
            'envios.*.id' => ['required', 'integer', 'exists:envio_marmitas,id'],
            'envios.*.quantidade_marmitas' => ['required', 'integer', 'min:1'],
            'envios.*.quantidade_agua' => ['required', 'integer', 'min:0'],
            'envios.*.endereco' => ['required', 'string'],
            'envios.*.notas' => ['nullable', 'string'],
            'envios.*.status' => ['required', Rule::in(EnvioMarmita::STATUS_ORDER)],
        ]);

        DB::transaction(function () use ($data): void {
            foreach ($data['envios'] as $envioData) {
                EnvioMarmita::query()
                    ->whereKey($envioData['id'])
                    ->update([
                        'quantidade_marmitas' => $envioData['quantidade_marmitas'],
                        'quantidade_agua' => $envioData['quantidade_agua'],
                        'endereco' => $envioData['endereco'],
                        'notas' => $envioData['notas'] ?? null,
                        'status' => $envioData['status'],
                    ]);
            }
        });

        $updateService->refreshState();

        return back()->with('status', 'Alteracoes de envios salvas com sucesso.');
    }

    public function destroy(EnvioMarmita $envioMarmita, AdminUpdateService $updateService): RedirectResponse
    {
        $envioMarmita->delete();
        $updateService->refreshState();

        return back()->with('status', 'Envio removido com sucesso.');
    }
}
