<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function refresh(Request $request, AdminUpdateService $updateService): RedirectResponse
    {
        $updateService->refreshState();

        return back()->with('status', 'Sistema atualizado e cache limpo.');
    }
}
