<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class PedidoAjuda extends Model
{
    public const STATUS_EM_ANDAMENTO = 'em_andamento';

    public const STATUS_FEITO = 'feito';

    public const STATUS_ORDER = [
        self::STATUS_EM_ANDAMENTO,
        self::STATUS_FEITO,
    ];

    protected $table = 'pedidos_ajuda';

    protected $fillable = [
        'numero_sequencial',
        'codigo_publico',
        'nome_recebedor',
        'cpf',
        'telefone',
        'endereco_completo_referencias',
        'itens',
        'status',
        'nome_normalizado',
        'endereco_normalizado',
        'telefone_normalizado',
        'cpf_normalizado',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $pedido): void {
            $pedido->nome_normalizado = self::normalizeText($pedido->nome_recebedor);
            $pedido->endereco_normalizado = self::normalizeText($pedido->endereco_completo_referencias);
            $pedido->telefone_normalizado = self::normalizePhone($pedido->telefone);
            $pedido->cpf_normalizado = self::normalizeCpf($pedido->cpf);
        });
    }

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

    public function controleEnvio(): HasOne
    {
        return $this->hasOne(ControleEnvio::class, 'pedido_id');
    }

    public function scopeOrderedByStatus(Builder $query): Builder
    {
        return $query
            ->orderByRaw("CASE status WHEN 'em_andamento' THEN 1 WHEN 'feito' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at');
    }

    public function getNumeroExibicaoAttribute(): string
    {
        return sprintf('#%05d', (int) $this->numero_sequencial);
    }

    public function getSlugCodigoAttribute(): string
    {
        return sprintf('pedido-%05d', (int) $this->numero_sequencial);
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

    public static function normalizeText(?string $value): string
    {
        $ascii = Str::ascii(Str::lower((string) $value));
        $normalized = preg_replace('/[^a-z0-9]+/', ' ', $ascii) ?? '';

        return trim(preg_replace('/\s+/', ' ', $normalized) ?? '');
    }

    public static function normalizePhone(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?? '';
    }

    public static function normalizeCpf(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?? '';
    }
}
