<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControleEnvio extends Model
{
    protected $table = 'controle_envios';

    protected $fillable = [
        'pedido_id',
        'nome_entregador',
        'notas',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(PedidoAjuda::class, 'pedido_id');
    }
}
