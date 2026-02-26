<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Services\AdminUpdateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    public function edit(): View
    {
        return view('admin.configuracoes.edit', [
            'configuracao' => Configuracao::singleton(),
        ]);
    }

    public function update(Request $request, AdminUpdateService $updateService): RedirectResponse
    {
        $data = $request->validate([
            'nome_paroquia' => ['required', 'string', 'max:255'],
            'texto_home' => ['nullable', 'string'],
            'chave_pix' => ['nullable', 'string', 'max:255'],
            'endereco' => ['nullable', 'string'],
            'google_maps_link' => ['nullable', 'url', 'max:255'],
        ]);

        $configuracao = Configuracao::singleton();
        $configuracao->update($data);
        $updateService->refreshState();

        return back()->with('status', 'Configurações atualizadas com sucesso.');
    }
}
