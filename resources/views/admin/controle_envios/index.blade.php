@extends('layouts.base')

@section('title', 'Admin - Controle de Envios')

@section('content')
    <main class="page page-admin has-sticky-save">
        <section class="header">
            <h1 class="title">Controle de Envios - {{ $configuracao->nome_paroquia }}</h1>
            <p class="subtitle">Gestao interna de pedidos, entregas e acompanhamento operacional.</p>
        </section>

        @include('admin.partials.nav')
        @include('admin.partials.live_sync')
        @include('partials.alerts')

        <section class="kpi-grid">
            <article class="card kpi-card">
                <p class="kpi-label">Em andamento</p>
                <p class="kpi-value">{{ $kpis['em_andamento'] }}</p>
            </article>
            <article class="card kpi-card">
                <p class="kpi-label">Feitos</p>
                <p class="kpi-value">{{ $kpis['feito'] }}</p>
            </article>
            <article class="card kpi-card">
                <p class="kpi-label">Total hoje</p>
                <p class="kpi-value">{{ $kpis['total_hoje'] }}</p>
            </article>
            <article class="card kpi-card">
                <p class="kpi-label">Total 7 dias</p>
                <p class="kpi-value">{{ $kpis['total_7_dias'] }}</p>
            </article>
        </section>

        <section class="card mt-16">
            <h2 class="section-title">Registrar pedido interno</h2>

            <form method="POST" action="{{ route('admin.controle-envios.store') }}" class="form-grid">
                @csrf

                <div class="form-grid-two">
                    <div>
                        <label for="nome_recebedor">Nome recebedor</label>
                        <input id="nome_recebedor" name="nome_recebedor" type="text" value="{{ old('nome_recebedor') }}" maxlength="255" required>
                    </div>

                    <div>
                        <label for="telefone">Telefone</label>
                        <input id="telefone" name="telefone" type="text" value="{{ old('telefone') }}" maxlength="32" required>
                    </div>
                </div>

                <div class="form-grid-two">
                    <div>
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach ($statusLabels as $status => $label)
                                <option value="{{ $status }}" @selected(old('status', 'em_andamento') === $status)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="nome_entregador">Nome entregador</label>
                        <input id="nome_entregador" name="nome_entregador" type="text" value="{{ old('nome_entregador') }}" maxlength="255">
                    </div>
                </div>

                <div>
                    <label for="endereco_completo_referencias">Endereco completo e referencias</label>
                    <textarea id="endereco_completo_referencias" name="endereco_completo_referencias" required>{{ old('endereco_completo_referencias') }}</textarea>
                </div>

                <div>
                    <label for="itens">Itens solicitados</label>
                    <textarea id="itens" name="itens" required>{{ old('itens') }}</textarea>
                </div>

                <div>
                    <label for="notas">Notas internas</label>
                    <textarea id="notas" name="notas">{{ old('notas') }}</textarea>
                </div>

                <button type="submit" class="button-primary">Registrar pedido</button>
            </form>
        </section>

        <section class="card mt-16">
            <h2 class="section-title">Lista de pedidos</h2>

            <form method="GET" action="{{ route('admin.controle-envios.index') }}" class="form-grid">
                <div class="form-grid-two">
                    <div>
                        <label for="filtro-status">Status</label>
                        <select id="filtro-status" name="status">
                            <option value="">Todos</option>
                            @foreach ($statusLabels as $status => $label)
                                <option value="{{ $status }}" @selected($statusFiltro === $status)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filtro-periodo">Periodo</label>
                        <select id="filtro-periodo" name="periodo">
                            <option value="">Todos</option>
                            <option value="hoje" @selected($periodoFiltro === 'hoje')>Hoje</option>
                            <option value="7dias" @selected($periodoFiltro === '7dias')>Ultimos 7 dias</option>
                            <option value="30dias" @selected($periodoFiltro === '30dias')>Ultimos 30 dias</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-two">
                    <div>
                        <label for="filtro-busca">Busca por nome ou telefone</label>
                        <input id="filtro-busca" name="busca" type="text" value="{{ $busca }}" maxlength="255">
                    </div>

                    <div class="actions-inline-desktop">
                        <button type="submit" class="button-primary">Filtrar</button>
                        <a class="button button-neutral" href="{{ route('admin.controle-envios.index') }}">Limpar filtros</a>
                    </div>
                </div>
            </form>

            @if ($pedidos->isEmpty())
                <p class="empty mt-16">Nenhum pedido encontrado.</p>
            @else
                <div class="controle-envios-grid mt-16">
                    @foreach ($pedidos as $pedido)
                        <article class="controle-envio-card">
                            <div class="controle-envio-head">
                                <p class="controle-envio-number">{{ $pedido->numero_exibicao }}</p>
                                <span class="badge badge-status-{{ $pedido->status }}">
                                    {{ $statusLabels[$pedido->status] ?? ucfirst($pedido->status) }}
                                </span>
                            </div>

                            <div class="controle-envio-form">
                                <input
                                    type="hidden"
                                    name="envios[{{ $pedido->id }}][id]"
                                    value="{{ $pedido->id }}"
                                    form="bulk-update-controle-envios"
                                >

                                <div class="form-grid-two">
                                    <div>
                                        <label for="pedido-{{ $pedido->id }}-nome">Nome recebedor</label>
                                        <input
                                            id="pedido-{{ $pedido->id }}-nome"
                                            name="envios[{{ $pedido->id }}][nome_recebedor]"
                                            type="text"
                                            maxlength="255"
                                            value="{{ $pedido->nome_recebedor }}"
                                            required
                                            form="bulk-update-controle-envios"
                                        >
                                    </div>

                                    <div>
                                        <label for="pedido-{{ $pedido->id }}-telefone">Telefone</label>
                                        <input
                                            id="pedido-{{ $pedido->id }}-telefone"
                                            name="envios[{{ $pedido->id }}][telefone]"
                                            type="text"
                                            maxlength="32"
                                            value="{{ $pedido->telefone }}"
                                            required
                                            form="bulk-update-controle-envios"
                                        >
                                    </div>
                                </div>

                                <div class="form-grid-two">
                                    <div>
                                        <label for="pedido-{{ $pedido->id }}-status">Status</label>
                                        <select
                                            id="pedido-{{ $pedido->id }}-status"
                                            name="envios[{{ $pedido->id }}][status]"
                                            required
                                            form="bulk-update-controle-envios"
                                        >
                                            @foreach ($statusLabels as $status => $label)
                                                <option value="{{ $status }}" @selected($pedido->status === $status)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="pedido-{{ $pedido->id }}-entregador">Nome entregador</label>
                                        <input
                                            id="pedido-{{ $pedido->id }}-entregador"
                                            name="envios[{{ $pedido->id }}][nome_entregador]"
                                            type="text"
                                            maxlength="255"
                                            value="{{ old('envios.'.$pedido->id.'.nome_entregador', $pedido->controleEnvio?->nome_entregador) }}"
                                            form="bulk-update-controle-envios"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label for="pedido-{{ $pedido->id }}-endereco">Endereco completo e referencias</label>
                                    <textarea
                                        id="pedido-{{ $pedido->id }}-endereco"
                                        name="envios[{{ $pedido->id }}][endereco_completo_referencias]"
                                        required
                                        form="bulk-update-controle-envios"
                                    >{{ $pedido->endereco_completo_referencias }}</textarea>
                                </div>

                                <div>
                                    <label for="pedido-{{ $pedido->id }}-itens">Itens solicitados</label>
                                    <textarea
                                        id="pedido-{{ $pedido->id }}-itens"
                                        name="envios[{{ $pedido->id }}][itens]"
                                        required
                                        form="bulk-update-controle-envios"
                                    >{{ $pedido->itens }}</textarea>
                                </div>

                                <div>
                                    <label for="pedido-{{ $pedido->id }}-notas">Notas internas</label>
                                    <textarea
                                        id="pedido-{{ $pedido->id }}-notas"
                                        name="envios[{{ $pedido->id }}][notas]"
                                        form="bulk-update-controle-envios"
                                    >{{ old('envios.'.$pedido->id.'.notas', $pedido->controleEnvio?->notas) }}</textarea>
                                </div>
                            </div>

                            <div class="controle-envio-actions">
                                <a
                                    class="button button-neutral"
                                    href="{{ route('pedido.show', ['codigo' => sprintf('%05d', (int) $pedido->numero_sequencial)]) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Ver ticket
                                </a>

                                <form method="POST" action="{{ route('admin.controle-envios.destroy', $pedido) }}" class="controle-envio-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button-danger" onclick="return confirm('Deseja excluir este pedido?')">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <form id="bulk-update-controle-envios" method="POST" action="{{ route('admin.controle-envios.bulk-update') }}">
                    @csrf
                    @method('PUT')
                </form>

                <div class="sticky-savebar" role="region" aria-label="Salvar alteracoes do controle de envios">
                    <div class="sticky-savebar-inner">
                        <button type="submit" form="bulk-update-controle-envios" class="button button-primary sticky-save-button">
                            Salvar alteracoes
                        </button>
                    </div>
                </div>
            @endif
        </section>

        <section class="card mt-16">
            <h2 class="section-title">Solicitantes recorrentes</h2>

            @if ($recorrentes->isEmpty())
                <p class="empty">Nenhuma recorrencia identificada ate o momento.</p>
            @else
                <div class="recorrentes-grid">
                    @foreach ($recorrentes as $recorrente)
                        <article class="recorrente-card">
                            <h3 class="item-name">{{ $recorrente['nome_recebedor'] }}</h3>
                            <p class="priority">Telefone: {{ $recorrente['telefone'] }}</p>
                            <p class="priority">Pedidos: {{ $recorrente['quantidade_pedidos'] }}</p>
                            <p class="priority">Ultima solicitacao: {{ $recorrente['ultima_solicitacao']->format('d/m/Y H:i') }}</p>
                            <p class="priority">Intervalo desde ultimo pedido: {{ $recorrente['intervalo_dias'] }} dia(s)</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
@endsection
