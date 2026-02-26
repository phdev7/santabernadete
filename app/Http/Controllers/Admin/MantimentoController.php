<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Models\Mantimento;
use App\Services\AdminUpdateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MantimentoController extends Controller
{
    public function index(Request $request): View
    {
        $statusFiltro = $request->query('status');

        $query = Mantimento::query()->orderedByPriority();

        if (in_array($statusFiltro, Mantimento::STATUS_ORDER, true)) {
            $query->where('status', $statusFiltro);
        } else {
            $statusFiltro = null;
        }

        return view('admin.mantimentos.index', [
            'mantimentos' => $query->get(),
            'statusFiltro' => $statusFiltro,
            'statusLabels' => Mantimento::statusLabels(),
            'configuracao' => Configuracao::singleton(),
        ]);
    }

    public function store(Request $request, AdminUpdateService $updateService): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(Mantimento::STATUS_ORDER)],
        ]);

        Mantimento::query()->create($data);
        $updateService->refreshState();

        return back()->with('status', 'Mantimento criado com sucesso.');
    }

    public function update(Request $request, Mantimento $mantimento, AdminUpdateService $updateService): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(Mantimento::STATUS_ORDER)],
        ]);

        $mantimento->update($data);
        $updateService->refreshState();

        return back()->with('status', 'Mantimento atualizado com sucesso.');
    }

    public function bulkUpdate(Request $request, AdminUpdateService $updateService): RedirectResponse
    {
        $data = $request->validate([
            'mantimentos' => ['required', 'array', 'min:1'],
            'mantimentos.*.id' => ['required', 'integer', 'exists:mantimentos,id'],
            'mantimentos.*.nome' => ['required', 'string', 'max:255'],
            'mantimentos.*.status' => ['required', Rule::in(Mantimento::STATUS_ORDER)],
        ]);

        DB::transaction(function () use ($data): void {
            foreach ($data['mantimentos'] as $mantimentoData) {
                Mantimento::query()
                    ->whereKey($mantimentoData['id'])
                    ->update([
                        'nome' => $mantimentoData['nome'],
                        'status' => $mantimentoData['status'],
                    ]);
            }
        });

        $updateService->refreshState();

        return back()->with('status', 'Alterações salvas com sucesso.');
    }

    public function destroy(Mantimento $mantimento, AdminUpdateService $updateService): RedirectResponse
    {
        $mantimento->delete();
        $updateService->refreshState();

        return back()->with('status', 'Mantimento excluído com sucesso.');
    }
}
