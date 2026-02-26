<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use App\Models\Mantimento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMantimentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_precisa_estar_autenticado_para_acessar_o_painel(): void
    {
        $this->get(route('admin.mantimentos.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_autenticado_consegue_cadastrar_mantimento_e_atualizar_timestamp_global(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-26 08:00:00'));
        $configuracao = Configuracao::singleton();
        $inicial = $configuracao->updated_at;

        $admin = User::factory()->create();
        Carbon::setTestNow(Carbon::parse('2026-02-26 08:10:00'));

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.mantimentos.store'), [
                'nome' => 'Água',
                'status' => 'vermelho',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mantimentos', [
            'nome' => 'Água',
            'status' => 'vermelho',
        ]);

        $configuracao->refresh();
        $this->assertTrue($configuracao->updated_at->greaterThan($inicial));

        Carbon::setTestNow();
    }

    public function test_admin_consegue_salvar_alteracoes_em_lote_de_mantimentos(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-26 09:00:00'));
        $configuracao = Configuracao::singleton();
        $inicial = $configuracao->updated_at;

        $mantimentoA = Mantimento::query()->create([
            'nome' => 'Arroz',
            'status' => 'vermelho',
        ]);

        $mantimentoB = Mantimento::query()->create([
            'nome' => 'Feijao',
            'status' => 'abastecido',
        ]);

        $admin = User::factory()->create();
        Carbon::setTestNow(Carbon::parse('2026-02-26 09:10:00'));

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.mantimentos.bulk-update'), [
                'mantimentos' => [
                    [
                        'id' => $mantimentoA->id,
                        'nome' => 'Arroz integral',
                        'status' => 'amarelo',
                    ],
                    [
                        'id' => $mantimentoB->id,
                        'nome' => 'Feijao carioca',
                        'status' => 'vermelho',
                    ],
                ],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('mantimentos', [
            'id' => $mantimentoA->id,
            'nome' => 'Arroz integral',
            'status' => 'amarelo',
        ]);

        $this->assertDatabaseHas('mantimentos', [
            'id' => $mantimentoB->id,
            'nome' => 'Feijao carioca',
            'status' => 'vermelho',
        ]);

        $configuracao->refresh();
        $this->assertTrue($configuracao->updated_at->greaterThan($inicial));

        Carbon::setTestNow();
    }
}
