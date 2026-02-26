@extends('layouts.base')

@section('title', 'Admin - Envios Marmitas')

@section('content')
    <main class="page page-admin has-sticky-save">
        <section class="header">
            <h1 class="title">Envios de Marmitas - {{ $configuracao->nome_paroquia }}</h1>
            <p class="subtitle">Controle interno de entregas com status em andamento e feito.</p>
        </section>

        @include('admin.partials.nav')
        @include('admin.partials.live_sync')
        @include('partials.alerts')

        <section class="kpi-grid">
            <article class="card kpi-card">
                <p class="kpi-label">Em andamento</p>
                <p class="kpi-value">{{ $resumo['em_andamento'] }}</p>
            </article>
            <article class="card kpi-card">
                <p class="kpi-label">Feitos</p>
                <p class="kpi-value">{{ $resumo['feito'] }}</p>
            </article>
            <article class="card kpi-card">
                <p class="kpi-label">Marmitas pendentes</p>
                <p class="kpi-value">{{ $resumo['marmitas_pendentes'] }}</p>
            </article>
            <article class="card kpi-card">
                <p class="kpi-label">Agua pendente</p>
                <p class="kpi-value">{{ $resumo['agua_pendente'] }}</p>
            </article>
        </section>

        <section class="card mt-16">
            <h2 class="section-title">Registrar novo envio</h2>

            <form method="POST" action="{{ route('admin.envios-marmitas.store') }}" class="form-grid">
                @csrf

                <div class="form-grid-two">
                    <div>
                        <label for="quantidade_marmitas">Quantidade de marmitas</label>
                        <input id="quantidade_marmitas" name="quantidade_marmitas" type="number" min="1" value="{{ old('quantidade_marmitas', 1) }}" required>
                    </div>

                    <div>
                        <label for="quantidade_agua">Quantidade de agua</label>
                        <input id="quantidade_agua" name="quantidade_agua" type="number" min="0" value="{{ old('quantidade_agua', 0) }}" required>
                    </div>
                </div>

                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach ($statusLabels as $status => $label)
                            <option value="{{ $status }}" @selected(old('status', 'em_andamento') === $status)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="endereco">Endereco de entrega</label>
                    <textarea id="endereco" name="endereco" required>{{ old('endereco') }}</textarea>
                </div>

                <div>
                    <label for="notas">Notas</label>
                    <textarea id="notas" name="notas">{{ old('notas') }}</textarea>
                </div>

                <button type="submit" class="button-primary">Registrar envio</button>
            </form>
        </section>

        <section class="card mt-16">
            <h2 class="section-title">Lista de envios</h2>

            <div class="nav-links admin-filter-row">
                <a class="pill {{ $statusFiltro === null ? 'active' : '' }}" href="{{ route('admin.envios-marmitas.index') }}">Todos</a>
                @foreach ($statusLabels as $status => $label)
                    <a
                        class="pill {{ $statusFiltro === $status ? 'active' : '' }}"
                        href="{{ route('admin.envios-marmitas.index', ['status' => $status]) }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($envios->isEmpty())
                <p class="empty">Nenhum envio cadastrado.</p>
            @else
                <div class="envios-grid">
                    @foreach ($envios as $envio)
                        <article class="envio-card">
                            <div class="envio-form">
                                <input
                                    type="hidden"
                                    name="envios[{{ $envio->id }}][id]"
                                    value="{{ $envio->id }}"
                                    form="bulk-update-envios-form"
                                >

                                <div class="form-grid-two">
                                    <div>
                                        <label for="envio-{{ $envio->id }}-marmitas">Marmitas</label>
                                        <input
                                            id="envio-{{ $envio->id }}-marmitas"
                                            type="number"
                                            min="1"
                                            name="envios[{{ $envio->id }}][quantidade_marmitas]"
                                            value="{{ $envio->quantidade_marmitas }}"
                                            required
                                            form="bulk-update-envios-form"
                                        >
                                    </div>

                                    <div>
                                        <label for="envio-{{ $envio->id }}-agua">Agua</label>
                                        <input
                                            id="envio-{{ $envio->id }}-agua"
                                            type="number"
                                            min="0"
                                            name="envios[{{ $envio->id }}][quantidade_agua]"
                                            value="{{ $envio->quantidade_agua }}"
                                            required
                                            form="bulk-update-envios-form"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label for="envio-{{ $envio->id }}-status">Status</label>
                                    <select
                                        id="envio-{{ $envio->id }}-status"
                                        name="envios[{{ $envio->id }}][status]"
                                        required
                                        form="bulk-update-envios-form"
                                    >
                                        @foreach ($statusLabels as $status => $label)
                                            <option value="{{ $status }}" @selected($envio->status === $status)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="envio-{{ $envio->id }}-endereco">Endereco</label>
                                    <textarea
                                        id="envio-{{ $envio->id }}-endereco"
                                        name="envios[{{ $envio->id }}][endereco]"
                                        required
                                        form="bulk-update-envios-form"
                                    >{{ $envio->endereco }}</textarea>
                                </div>

                                <div>
                                    <label for="envio-{{ $envio->id }}-notas">Notas</label>
                                    <textarea
                                        id="envio-{{ $envio->id }}-notas"
                                        name="envios[{{ $envio->id }}][notas]"
                                        form="bulk-update-envios-form"
                                    >{{ $envio->notas }}</textarea>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.envios-marmitas.destroy', $envio) }}" class="envio-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-danger" onclick="return confirm('Deseja excluir este envio?')">
                                    Excluir
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>

                <form id="bulk-update-envios-form" method="POST" action="{{ route('admin.envios-marmitas.bulk-update') }}">
                    @csrf
                    @method('PUT')
                </form>

                <div class="sticky-savebar" role="region" aria-label="Salvar alteracoes de envios">
                    <div class="sticky-savebar-inner">
                        <button type="submit" form="bulk-update-envios-form" class="button button-primary sticky-save-button">
                            Salvar alteracoes
                        </button>
                    </div>
                </div>
            @endif
        </section>
    </main>
@endsection
