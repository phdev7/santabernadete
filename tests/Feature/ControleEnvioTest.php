<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use App\Models\PedidoAjuda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControleEnvioTest extends TestCase
{
    use RefreshDatabase;

    public function test_criacao_publica_exige_cpf(): void
    {
        $response = $this->post(route('preciso-ajuda.store'), [
            'nome_recebedor' => 'Maria',
            'cpf' => '',
            'telefone' => '(32) 99999-1000',
            'endereco_completo_referencias' => 'Rua A, 100',
            'itens' => 'Cesta basica',
        ]);

        $response->assertSessionHasErrors(['cpf']);
    }

    public function test_criacao_publica_exige_telefone(): void
    {
        $response = $this->post(route('preciso-ajuda.store'), [
            'nome_recebedor' => 'Maria',
            'cpf' => '123.456.789-10',
            'telefone' => '',
            'endereco_completo_referencias' => 'Rua A, 100',
            'itens' => 'Cesta basica',
        ]);

        $response->assertSessionHasErrors(['telefone']);
    }

    public function test_criacao_publica_gera_codigo_sequencial_sem_colisao(): void
    {
        $this->post(route('preciso-ajuda.store'), [
            'nome_recebedor' => 'Maria',
            'cpf' => '123.456.789-10',
            'telefone' => '(32) 99999-1000',
            'endereco_completo_referencias' => 'Rua A, 100',
            'itens' => 'Cesta basica',
        ])->assertRedirect();

        $this->post(route('preciso-ajuda.store'), [
            'nome_recebedor' => 'Joao',
            'cpf' => '987.654.321-00',
            'telefone' => '(32) 99999-2000',
            'endereco_completo_referencias' => 'Rua B, 200',
            'itens' => 'Agua',
        ])->assertRedirect();

        $this->assertDatabaseHas('pedidos_ajuda', [
            'numero_sequencial' => 1,
            'codigo_publico' => 'pedido-00001',
        ]);

        $this->assertDatabaseHas('pedidos_ajuda', [
            'numero_sequencial' => 2,
            'codigo_publico' => 'pedido-00002',
        ]);
    }

    public function test_ticket_publico_e_acessivel_sem_auth_com_dados_do_pedido(): void
    {
        $pedido = $this->criarPedido([
            'numero_sequencial' => 1,
            'codigo_publico' => 'pedido-00001',
            'nome_recebedor' => 'Maria',
            'cpf' => '123.456.789-10',
            'telefone' => '32999991000',
            'endereco_completo_referencias' => 'Rua A, 100',
            'itens' => 'Cobertores',
            'status' => PedidoAjuda::STATUS_EM_ANDAMENTO,
        ], [
            'nome_entregador' => 'Carlos',
            'notas' => 'Entregar apos 18h',
        ]);

        $this->get(route('pedido.show', ['codigo' => '00001']))
            ->assertOk()
            ->assertSee($pedido->numero_exibicao)
            ->assertSee('Maria')
            ->assertSee('123.456.789-10')
            ->assertSee('Carlos')
            ->assertSee('Entregar apos 18h');
    }

    public function test_admin_consegue_salvar_alteracoes_em_lote_do_controle_envios(): void
    {
        $pedido = $this->criarPedido([
            'numero_sequencial' => 1,
            'codigo_publico' => 'pedido-00001',
            'nome_recebedor' => 'Maria',
            'cpf' => '123.456.789-10',
            'telefone' => '32999991000',
            'endereco_completo_referencias' => 'Rua A, 100',
            'itens' => 'Agua',
            'status' => PedidoAjuda::STATUS_EM_ANDAMENTO,
        ], [
            'nome_entregador' => null,
            'notas' => null,
        ]);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.controle-envios.bulk-update'), [
                'envios' => [
                    [
                        'id' => $pedido->id,
                        'nome_recebedor' => 'Maria Souza',
                        'cpf' => '222.333.444-55',
                        'telefone' => '(32) 98888-1000',
                        'endereco_completo_referencias' => 'Rua A, 110',
                        'itens' => 'Agua e alimentos',
                        'nome_entregador' => 'Pedro',
                        'notas' => 'Prioridade alta',
                        'status' => PedidoAjuda::STATUS_FEITO,
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pedidos_ajuda', [
            'id' => $pedido->id,
            'nome_recebedor' => 'Maria Souza',
            'cpf' => '222.333.444-55',
            'telefone' => '(32) 98888-1000',
            'status' => PedidoAjuda::STATUS_FEITO,
        ]);

        $this->assertDatabaseHas('controle_envios', [
            'pedido_id' => $pedido->id,
            'nome_entregador' => 'Pedro',
            'notas' => 'Prioridade alta',
        ]);
    }

    public function test_criacao_publica_incrementa_sync_version(): void
    {
        $configuracao = Configuracao::singleton();
        $versaoInicial = (int) $configuracao->sync_version;

        $this->post(route('preciso-ajuda.store'), [
            'nome_recebedor' => 'Maria',
            'cpf' => '123.456.789-10',
            'telefone' => '(32) 99999-1000',
            'endereco_completo_referencias' => 'Rua A, 100',
            'itens' => 'Cesta basica',
        ])->assertRedirect();

        $configuracao->refresh();
        $this->assertGreaterThan($versaoInicial, (int) $configuracao->sync_version);
    }

    public function test_dashboard_admin_mostra_recorrencia_por_regra_normalizada_exata(): void
    {
        $this->criarPedido([
            'numero_sequencial' => 1,
            'codigo_publico' => 'pedido-00001',
            'nome_recebedor' => 'Maria Souza',
            'cpf' => '123.456.789-10',
            'telefone' => '(32) 99999-1000',
            'endereco_completo_referencias' => 'Rua A, 10 - Centro',
            'itens' => 'Agua',
            'status' => PedidoAjuda::STATUS_EM_ANDAMENTO,
        ], [
            'nome_entregador' => null,
            'notas' => null,
        ]);

        $this->criarPedido([
            'numero_sequencial' => 2,
            'codigo_publico' => 'pedido-00002',
            'nome_recebedor' => '  maria souza ',
            'cpf' => '12345678910',
            'telefone' => '32 99999-1000',
            'endereco_completo_referencias' => 'Rua A 10 Centro',
            'itens' => 'Alimentos',
            'status' => PedidoAjuda::STATUS_EM_ANDAMENTO,
        ], [
            'nome_entregador' => null,
            'notas' => null,
        ]);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.controle-envios.index'))
            ->assertOk()
            ->assertSee('Solicitantes recorrentes')
            ->assertSee('Pedidos: 2');
    }

    /**
     * @param  array<string, mixed>  $pedidoData
     * @param  array<string, mixed>  $controleData
     */
    private function criarPedido(array $pedidoData, array $controleData): PedidoAjuda
    {
        $pedido = PedidoAjuda::query()->create($pedidoData);
        $pedido->controleEnvio()->create($controleData);

        return $pedido;
    }
}
