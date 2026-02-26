@extends('layouts.base')

@section('title', 'Admin - Mantimentos')

@section('content')
    <main class="page page-admin has-sticky-save">
        <section class="header">
            <h1 class="title">Admin - {{ $configuracao->nome_paroquia }}</h1>
            <p class="subtitle">Gestão de mantimentos e prioridade de atendimento.</p>
        </section>

        @include('admin.partials.nav')
        @include('admin.partials.live_sync')
        @include('partials.alerts')

        <section class="card">
            <h2 class="section-title">Novo Mantimento</h2>

            <form method="POST" action="{{ route('admin.mantimentos.store') }}" class="form-grid">
                @csrf

                <div>
                    <label for="nome">Nome do item</label>
                    <input id="nome" name="nome" type="text" value="{{ old('nome') }}" maxlength="255" required>
                </div>

                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach ($statusLabels as $status => $label)
                            <option value="{{ $status }}" @selected(old('status', 'vermelho') === $status)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="button-primary">Cadastrar item</button>
            </form>
        </section>

        <section class="card mt-16">
            <h2 class="section-title">Lista Completa</h2>

            <div class="nav-links admin-filter-row">
                <a class="pill {{ $statusFiltro === null ? 'active' : '' }}" href="{{ route('admin.mantimentos.index') }}">Todos</a>
                @foreach ($statusLabels as $status => $label)
                    <a
                        class="pill {{ $statusFiltro === $status ? 'active' : '' }}"
                        href="{{ route('admin.mantimentos.index', ['status' => $status]) }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($mantimentos->isEmpty())
                <p class="empty">Nenhum mantimento cadastrado.</p>
            @else
                <div class="mantimentos-grid">
                    @foreach ($mantimentos as $mantimento)
                        <article class="mantimento-card">
                            <div class="mantimento-form">
                                <input
                                    type="hidden"
                                    name="mantimentos[{{ $mantimento->id }}][id]"
                                    value="{{ $mantimento->id }}"
                                    form="bulk-update-form"
                                >

                                <div class="form-grid-two">
                                    <div>
                                        <label for="nome-{{ $mantimento->id }}">Nome</label>
                                        <input
                                            id="nome-{{ $mantimento->id }}"
                                            type="text"
                                            name="mantimentos[{{ $mantimento->id }}][nome]"
                                            value="{{ $mantimento->nome }}"
                                            maxlength="255"
                                            required
                                            form="bulk-update-form"
                                        >
                                    </div>

                                    <div>
                                        <label for="status-{{ $mantimento->id }}">Status</label>
                                        <select
                                            id="status-{{ $mantimento->id }}"
                                            name="mantimentos[{{ $mantimento->id }}][status]"
                                            required
                                            form="bulk-update-form"
                                        >
                                            @foreach ($statusLabels as $status => $label)
                                                <option value="{{ $status }}" @selected($mantimento->status === $status)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.mantimentos.destroy', $mantimento) }}" class="mantimento-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-danger" onclick="return confirm('Deseja excluir este item?')">
                                    Excluir
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>

                <form id="bulk-update-form" method="POST" action="{{ route('admin.mantimentos.bulk-update') }}">
                    @csrf
                    @method('PUT')
                </form>

                <div class="sticky-savebar" role="region" aria-label="Salvar alterações">
                    <div class="sticky-savebar-inner">
                        <button type="submit" form="bulk-update-form" class="button button-primary sticky-save-button">
                            Salvar alterações
                        </button>
                    </div>
                </div>
            @endif
        </section>
    </main>
@endsection
