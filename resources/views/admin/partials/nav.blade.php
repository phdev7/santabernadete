<div class="top-nav" data-admin-nav>
    <div class="admin-nav-head">
        <button
            type="button"
            class="admin-menu-toggle"
            data-admin-menu-toggle
            aria-expanded="false"
            aria-controls="adminMenu"
        >
            Menu
        </button>
    </div>

    <div id="adminMenu" class="admin-menu" data-admin-menu>
        <nav class="nav-links" aria-label="Navegacao do painel">
            <a class="pill {{ request()->routeIs('admin.mantimentos.*') ? 'active' : '' }}" href="{{ route('admin.mantimentos.index') }}">
                Mantimentos
            </a>
            <a class="pill {{ request()->routeIs('admin.controle-envios.*') ? 'active' : '' }}" href="{{ route('admin.controle-envios.index') }}">
                Controle de Envios
            </a>
            <a class="pill {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}" href="{{ route('admin.configuracoes.edit') }}">
                Configuracoes
            </a>
        </nav>

        <form method="POST" action="{{ route('admin.logout') }}" class="admin-logout-form">
            @csrf
            <button type="submit" class="pill">Sair</button>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.querySelectorAll('[data-admin-nav]').forEach((container) => {
            const toggle = container.querySelector('[data-admin-menu-toggle]');
            const menu = container.querySelector('[data-admin-menu]');

            if (!toggle || !menu) {
                return;
            }

            container.classList.add('admin-nav-ready');

            toggle.addEventListener('click', () => {
                const isOpen = menu.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });
    </script>
@endpush
