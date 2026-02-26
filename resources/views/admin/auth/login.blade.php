@extends('layouts.base')

@section('title', 'Login Admin')

@section('content')
    <main class="page page-auth">
        <section class="header">
            <h1 class="title">Painel Administrativo</h1>
            <p class="subtitle">Acesso restrito para equipe da paróquia.</p>
        </section>

        <section class="card">
            @include('partials.alerts')

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="form-grid">
                @csrf
                <div>
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div>
                    <label for="password">Senha</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                </div>

                <button type="submit" class="button-primary">Entrar</button>
            </form>
        </section>
    </main>
@endsection
