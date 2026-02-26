@extends('layouts.base')

@section('title', 'Central de Doações - ' . $configuracao->nome_paroquia)

@section('content')
    <main class="page page-public">
        <header class="header">
            <h1 class="title">{{ $configuracao->nome_paroquia }}</h1>
            <p class="subtitle">
                {{ $configuracao->texto_home ?: 'Central de doações para apoiar famílias afetadas pelas enchentes.' }}
            </p>
            <p class="city">Ubá/MG</p>
            <p class="last-update">
                Última atualização há
                {{ $ultimaAtualizacaoMinutos === 1 ? '1 minuto' : $ultimaAtualizacaoMinutos . ' minutos' }}
            </p>
        </header>

        <div class="public-grid">
            <section class="card">
                <h2 class="section-title">Lista de Mantimentos</h2>

                @if ($mantimentos->isEmpty())
                    <p class="empty">Nenhum item cadastrado no momento.</p>
                @else
                    <div class="list">
                        @foreach ($mantimentos as $mantimento)
                            <article class="item">
                                <div class="item-head">
                                    <h3 class="item-name">{{ $mantimento->nome }}</h3>
                                    <span class="badge badge-{{ $mantimento->status }}">
                                        {{ $statusLabels[$mantimento->status] ?? ucfirst($mantimento->status) }}
                                    </span>
                                </div>
                                <p class="priority">
                                    Prioridade:
                                    {{ $mantimento->status === 'vermelho' ? 'Alta' : ($mantimento->status === 'amarelo' ? 'Média' : 'Baixa') }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <aside class="public-side">
                <section class="card">
                    <h2 class="section-title">Classificação</h2>
                    <ul class="status-legend">
                        <li class="legend-item">
                            <span class="dot dot-critical" aria-hidden="true"></span>
                            Necessário - Crítico
                        </li>
                        <li class="legend-item">
                            <span class="dot dot-moderate" aria-hidden="true"></span>
                            Necessário - Moderado
                        </li>
                        <li class="legend-item">
                            <span class="dot dot-filled" aria-hidden="true"></span>
                            Abastecido
                        </li>
                    </ul>
                </section>

                <section class="card">
                    <h2 class="section-title">Ajude Agora</h2>
                    <p class="subtitle">Consulte endereço e chave PIX para enviar sua doação.</p>
                    <div class="actions">
                        <a class="button button-primary" href="{{ route('doar') }}">Quero Doar</a>
                    </div>
                </section>
            </aside>
        </div>

        @if ($mantimentos->isNotEmpty())
            <section class="card mt-16">
                <p class="small">
                    A lista é ordenada automaticamente por prioridade: crítico, moderado e abastecido.
                </p>
            </section>
        @endif
    </main>
@endsection
