<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Mantimento extends Model
{
    public const STATUS_VERMELHO = 'vermelho';

    public const STATUS_AMARELO = 'amarelo';

    public const STATUS_ABASTECIDO = 'abastecido';

    public const STATUS_ORDER = [
        self::STATUS_VERMELHO,
        self::STATUS_AMARELO,
        self::STATUS_ABASTECIDO,
    ];

    protected $fillable = [
        'nome',
        'status',
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

    public function scopeOrderedByPriority(Builder $query): Builder
    {
        return $query
            ->orderByRaw("CASE status WHEN 'vermelho' THEN 1 WHEN 'amarelo' THEN 2 WHEN 'abastecido' THEN 3 ELSE 4 END")
            ->orderBy('nome');
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_VERMELHO => 'Necessário - Crítico',
            self::STATUS_AMARELO => 'Necessário - Moderado',
            self::STATUS_ABASTECIDO => 'Abastecido',
        ];
    }
}
