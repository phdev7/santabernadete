@extends('layouts.base')

@section('title', 'Preciso de Ajuda - ' . $configuracao->nome_paroquia)

@section('content')
    <main class="page page-public">
        <header class="header">
            <h1 class="title">Preciso de ajuda</h1>
            <p class="subtitle">Preencha os dados para registrar um pedido de apoio da {{ $configuracao->nome_paroquia }}.</p>
        </header>

        @include('partials.alerts')

        <section class="card">
            <form method="POST" action="{{ route('preciso-ajuda.store') }}" class="form-grid">
                @csrf

                <div class="form-grid-two">
                    <div>
                        <label for="nome_recebedor">Nome</label>
                        <input id="nome_recebedor" name="nome_recebedor" type="text" value="{{ old('nome_recebedor') }}" maxlength="255" required>
                    </div>

                    <div>
                        <label for="telefone">Telefone</label>
                        <input id="telefone" name="telefone" type="text" value="{{ old('telefone') }}" maxlength="32" required>
                    </div>
                </div>

                <div>
                    <label for="endereco_completo_referencias">Endereco completo e referencias</label>
                    <textarea id="endereco_completo_referencias" name="endereco_completo_referencias" required>{{ old('endereco_completo_referencias') }}</textarea>
                </div>

                <div>
                    <label for="itens">Itens necessarios</label>
                    <textarea id="itens" name="itens" required>{{ old('itens') }}</textarea>
                </div>

                <button type="submit" class="button-primary">Enviar pedido</button>
            </form>
        </section>

        <div class="actions">
            <a class="button button-neutral" href="{{ route('home') }}">Voltar para pagina inicial</a>
        </div>
    </main>
@endsection
