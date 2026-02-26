<?php

namespace Tests\Feature;

use App\Models\EnvioMarmita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEnvioMarmitaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_precisa_estar_autenticado_para_acessar_envios_marmitas(): void
    {
        $this->get(route('admin.envios-marmitas.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_consegue_registrar_envio_marmitas(): void
    {
        $admin = User::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.envios-marmitas.store'), [
                'quantidade_marmitas' => 40,
                'quantidade_agua' => 24,
                'endereco' => 'Rua das Flores, 123',
                'notas' => 'Entregar na recepcao.',
                'status' => 'em_andamento',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('envio_marmitas', [
            'quantidade_marmitas' => 40,
            'quantidade_agua' => 24,
            'endereco' => 'Rua das Flores, 123',
            'status' => 'em_andamento',
        ]);
    }

    public function test_admin_consegue_atualizar_envios_em_lote_e_marcar_como_feito(): void
    {
        $admin = User::factory()->create();

        $envio = EnvioMarmita::query()->create([
            'quantidade_marmitas' => 20,
            'quantidade_agua' => 10,
            'endereco' => 'Av. Principal, 500',
            'notas' => null,
            'status' => 'em_andamento',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.envios-marmitas.bulk-update'), [
                'envios' => [
                    [
                        'id' => $envio->id,
                        'quantidade_marmitas' => 22,
                        'quantidade_agua' => 12,
                        'endereco' => 'Av. Principal, 500 - bloco B',
                        'notas' => 'Concluido',
                        'status' => 'feito',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('envio_marmitas', [
            'id' => $envio->id,
            'quantidade_marmitas' => 22,
            'quantidade_agua' => 12,
            'endereco' => 'Av. Principal, 500 - bloco B',
            'notas' => 'Concluido',
            'status' => 'feito',
        ]);
    }
}
