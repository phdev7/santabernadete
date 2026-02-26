<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

class ConfiguracaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuracao::query()->updateOrCreate(
            ['id' => 1],
            [
                'nome_paroquia' => 'Paróquia Santa Bernadete',
                'texto_home' => 'Central de doações para apoiar famílias afetadas pelas enchentes em Ubá/MG.',
                'chave_pix' => '',
                'endereco' => '',
                'google_maps_link' => '',
            ],
        );
    }
}
