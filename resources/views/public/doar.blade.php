@extends('layouts.base')

@section('title', 'Doação - ' . $configuracao->nome_paroquia)

@section('content')
    <main class="page page-public">
        <header class="header">
            <h1 class="title">Como Doar</h1>
            <p class="subtitle">Dados oficiais para doação na {{ $configuracao->nome_paroquia }}.</p>
        </header>

        <section class="form-grid-two">
            <article class="card">
                <h2 class="section-title">Endereço</h2>
                <p class="subtitle">{{ $configuracao->endereco ?: 'Endereço não informado.' }}</p>

                @if ($configuracao->google_maps_link)
                    <div class="actions">
                        <a href="{{ $configuracao->google_maps_link }}" target="_blank" rel="noopener noreferrer" class="button button-primary">
                            Abrir no Google Maps
                        </a>
                    </div>
                @endif
            </article>

            <article class="card">
                <h2 class="section-title">Chave PIX</h2>
                <input id="pixKey" type="text" readonly value="{{ $configuracao->chave_pix }}">
                <div class="actions">
                    <button type="button" id="copyPixButton" class="button-primary">Copiar chave</button>
                </div>
                <p id="copyHint" class="copy-hint">Toque no botão para copiar.</p>
            </article>
        </section>

        <div class="actions">
            <a class="button button-neutral" href="{{ route('home') }}">Voltar para lista de mantimentos</a>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        const button = document.getElementById('copyPixButton');
        const pixInput = document.getElementById('pixKey');
        const hint = document.getElementById('copyHint');

        button?.addEventListener('click', async () => {
            if (!pixInput.value) {
                hint.textContent = 'Nenhuma chave PIX cadastrada.';
                return;
            }

            try {
                await navigator.clipboard.writeText(pixInput.value);
                hint.textContent = 'Chave PIX copiada com sucesso.';
            } catch (error) {
                hint.textContent = 'Não foi possível copiar automaticamente.';
            }
        });
    </script>
@endpush
