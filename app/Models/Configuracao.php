<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'nome_paroquia',
        'texto_home',
        'chave_pix',
        'endereco',
        'google_maps_link',
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

    public static function singleton(): self
    {
        /** @var self $configuracao */
        $configuracao = self::query()->firstOrCreate(
            ['id' => 1],
            [
                'nome_paroquia' => 'Paróquia Santa Bernadete',
                'texto_home' => 'Central de doações para apoiar famílias afetadas pelas enchentes em Ubá/MG.',
                'chave_pix' => '',
                'endereco' => '',
                'google_maps_link' => '',
            ],
        );

        return $configuracao;
    }
}
