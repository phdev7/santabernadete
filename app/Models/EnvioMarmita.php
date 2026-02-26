<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EnvioMarmita extends Model
{
    public const STATUS_EM_ANDAMENTO = 'em_andamento';

    public const STATUS_FEITO = 'feito';

    public const STATUS_ORDER = [
        self::STATUS_EM_ANDAMENTO,
        self::STATUS_FEITO,
    ];

    protected $fillable = [
        'quantidade_marmitas',
        'quantidade_agua',
        'endereco',
        'notas',
        'status',
    ];

    public function scopeOrderedByStatus(Builder $query): Builder
    {
        return $query
            ->orderByRaw("CASE status WHEN 'em_andamento' THEN 1 WHEN 'feito' THEN 2 ELSE 3 END")
            ->orderByDesc('updated_at');
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_EM_ANDAMENTO => 'Em andamento',
            self::STATUS_FEITO => 'Feito',
        ];
    }
}
