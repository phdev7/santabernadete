<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use App\Models\Mantimento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_exibe_mantimentos_na_ordem_de_prioridade(): void
    {
        Configuracao::query()->create([
            'nome_paroquia' => 'Paroquia Santa Bernadete',
            'texto_home' => 'Texto de teste',
            'chave_pix' => 'pix-chave',
            'endereco' => 'Rua teste, 100',
            'google_maps_link' => 'https://maps.google.com',
        ]);

        Mantimento::query()->create([
            'nome' => 'Cobertor',
            'status' => 'abastecido',
        ]);

        Mantimento::query()->create([
            'nome' => 'Agua mineral',
            'status' => 'vermelho',
        ]);

        Mantimento::query()->create([
            'nome' => 'Leite',
            'status' => 'amarelo',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Agua mineral',
            'Leite',
            'Cobertor',
        ]);
        $response->assertSee('Ultima atualizacao ha');
        $response->assertSee('Quero doar');
        $response->assertSee('Preciso de ajuda');
    }
}
