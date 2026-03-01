@extends('layouts.base')

@section('title', $pedido->numero_exibicao . ' - Ticket de Pedido')

@section('content')
    <main class="page page-public">
        <header class="header">
            <h1 class="title">Ticket {{ $pedido->numero_exibicao }}</h1>
            <p class="subtitle">Comprovante publico do pedido registrado na {{ $configuracao->nome_paroquia }}.</p>
        </header>

        @include('partials.alerts')

        <section class="card ticket-card">
            <div class="item-head">
                <h2 class="section-title">{{ $pedido->numero_exibicao }}</h2>
                <span class="badge badge-status-{{ $pedido->status }}">
                    {{ $statusLabels[$pedido->status] ?? ucfirst($pedido->status) }}
                </span>
            </div>

            <div class="ticket-grid">
                <div>
                    <p class="ticket-label">Nome recebedor</p>
                    <p class="ticket-value">{{ $pedido->nome_recebedor }}</p>
                </div>
                <div>
                    <p class="ticket-label">Telefone</p>
                    <p class="ticket-value">{{ $pedido->telefone }}</p>
                </div>
                <div>
                    <p class="ticket-label">Endereco completo e referencias</p>
                    <p class="ticket-value">{{ $pedido->endereco_completo_referencias }}</p>
                </div>
                <div>
                    <p class="ticket-label">Itens solicitados</p>
                    <p class="ticket-value">{{ $pedido->itens }}</p>
                </div>
                <div>
                    <p class="ticket-label">Nome entregador</p>
                    <p class="ticket-value">{{ $pedido->controleEnvio?->nome_entregador ?: 'A definir' }}</p>
                </div>
                <div>
                    <p class="ticket-label">Notas</p>
                    <p class="ticket-value">{{ $pedido->controleEnvio?->notas ?: 'Sem notas' }}</p>
                </div>
                <div>
                    <p class="ticket-label">Criado em</p>
                    <p class="ticket-value">{{ $pedido->created_at?->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="ticket-label">Atualizado em</p>
                    <p class="ticket-value">{{ $pedido->updated_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </section>

        <div class="actions actions-inline-desktop">
            <button type="button" class="button-primary" onclick="window.print()">Imprimir / Baixar</button>
            <a class="button button-neutral" href="{{ route('home') }}">Voltar para inicio</a>
        </div>
    </main>
@endsection
