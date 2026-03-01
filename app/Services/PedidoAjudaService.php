<?php

namespace App\Services;

use App\Models\PedidoAjuda;
use Illuminate\Support\Facades\DB;

class PedidoAjudaService
{
    /**
     * @param array{
     *     nome_recebedor: string,
     *     telefone: string,
     *     endereco_completo_referencias: string,
     *     itens: string,
     *     status?: string,
     *     nome_entregador?: string|null,
     *     notas?: string|null
     * } $data
     */
    public function createWithControle(array $data): PedidoAjuda
    {
        return DB::transaction(function () use ($data): PedidoAjuda {
            $proximoNumero = ((int) PedidoAjuda::query()->lockForUpdate()->max('numero_sequencial')) + 1;

            $pedido = PedidoAjuda::query()->create([
                'numero_sequencial' => $proximoNumero,
                'codigo_publico' => sprintf('pedido-%05d', $proximoNumero),
                'nome_recebedor' => $data['nome_recebedor'],
                'telefone' => $data['telefone'],
                'endereco_completo_referencias' => $data['endereco_completo_referencias'],
                'itens' => $data['itens'],
                'status' => $data['status'] ?? PedidoAjuda::STATUS_EM_ANDAMENTO,
            ]);

            $pedido->controleEnvio()->create([
                'nome_entregador' => $data['nome_entregador'] ?? null,
                'notas' => $data['notas'] ?? null,
            ]);

            return $pedido;
        });
    }
}
