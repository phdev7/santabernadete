@extends('layouts.base')

@section('title', 'Admin - Configurações')

@section('content')
    <main class="page page-admin">
        <section class="header">
            <h1 class="title">Configurações Institucionais</h1>
            <p class="subtitle">Dados públicos exibidos na Home e na página de doação.</p>
        </section>

        @include('admin.partials.nav')
        @include('admin.partials.live_sync')
        @include('partials.alerts')

        <section class="card">
            <form method="POST" action="{{ route('admin.configuracoes.update') }}" class="form-grid">
                @csrf
                @method('PUT')

                <div class="form-grid-two">
                    <div>
                        <label for="nome_paroquia">Nome da Paróquia</label>
                        <input id="nome_paroquia" name="nome_paroquia" type="text" value="{{ old('nome_paroquia', $configuracao->nome_paroquia) }}" maxlength="255" required>
                    </div>

                    <div>
                        <label for="chave_pix">Chave PIX</label>
                        <input id="chave_pix" name="chave_pix" type="text" value="{{ old('chave_pix', $configuracao->chave_pix) }}" maxlength="255">
                    </div>

                    <div>
                        <label for="google_maps_link">Link do Google Maps</label>
                        <input id="google_maps_link" name="google_maps_link" type="url" value="{{ old('google_maps_link', $configuracao->google_maps_link) }}" maxlength="255">
                    </div>
                </div>

                <div>
                    <label for="texto_home">Texto institucional da Home</label>
                    <textarea id="texto_home" name="texto_home">{{ old('texto_home', $configuracao->texto_home) }}</textarea>
                </div>

                <div>
                    <label for="endereco">Endereço completo</label>
                    <textarea id="endereco" name="endereco">{{ old('endereco', $configuracao->endereco) }}</textarea>
                </div>

                <button type="submit" class="button-primary">Salvar configurações</button>
            </form>
        </section>
    </main>
@endsection
