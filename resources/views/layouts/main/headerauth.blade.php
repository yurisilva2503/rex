<header class="main-header bg-body shadow-sm border-bottom">
    <div class="container-fluid mb-1 mt-1">
        <div class="row align-items-center">
            <!-- Logo/App Name - Coluna fixa à esquerda -->
            <div class="col-auto p-2 px-3">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="d-flex">
                        <img src="{{ asset('/assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 50px;">
                        <p style="font-family: monospace;" class="fw-bold text-center m-0 fs-2 text-body">
                            {{ ucfirst(substr(env('APP_NAME'), 0, 1)) }}<span class="text-primary">{{ substr(env('APP_NAME'), 1) }}</span>
                        </p>
                    </div>
                </a>
            </div>

            <!-- Navegação central - Ocupa todo espaço restante -->
            <div class="col border-start">
                <nav class="navbar border-0 navbar-expand-lg p-0">
                    <div class="container-fluid justify-content-end justify-content-lg-start p-0">
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav">
                            @can('viewAny', App\Models\Indicator::class)
                            <li class="nav-item">
                                <a class="nav-link text-body {{ request()->routeIs('home') ? 'border-bottom border-primary' : '' }}"
                                aria-current="page" href="{{ route('home') }}">
                                    <i class="bi bi-house me-1"></i>Dashboard
                                </a>
                            </li>
                            @endcan

                            @can('viewAny', App\Models\Department::class)
                            <li class="nav-item">
                                <a class="nav-link text-body {{ request()->routeIs('departments') ? 'border-bottom border-primary' : '' }}"
                                aria-current="page" href="{{ route('departments') }}">
                                    <i class="bi bi-building me-1"></i>Departamentos
                                </a>
                            </li>
                            @endcan

                            @can('viewAny', App\Models\User::class)
                            <li class="nav-item">
                                <a class="nav-link text-body {{ request()->routeIs('users') ? 'border-bottom border-primary' : '' }}"
                                aria-current="page" href="{{ route('users') }}">
                                    <i class="bi bi-people me-1"></i>Usuários
                                </a>
                            </li>
                            @endcan
                        </ul>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Perfil e ações - Coluna fixa à direita -->
            <div class="col-12 col-sm-auto">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <!-- Seletor de tema -->
                    <div class="btn-group btn-group-sm" role="group" aria-label="Seletor de tema">
                        <input type="radio" class="btn-check" name="theme" id="theme-light" value="light" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="theme-light" title="Tema Claro">
                            <i class="bi bi-sun"></i>
                        </label>

                        <input type="radio" class="btn-check" name="theme" id="theme-dark" value="dark" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="theme-dark" title="Tema Escuro">
                            <i class="bi bi-moon-stars"></i>
                        </label>

                        <input type="radio" class="btn-check" name="theme" id="theme-auto" value="auto" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="theme-auto" title="Automático (sistema)">
                            <i class="bi bi-circle-half"></i>
                        </label>
                    </div>

                    <!-- Divisor vertical -->
                    <div class="vr"></div>

                    <!-- Nome do usuário e botão sair -->
                    <div class="d-flex align-items-center gap-2">
                        <a class="link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover text-decoration-none {{ request()->routeIs('profile') ? 'border-bottom border-primary' : '' }}"
                            href="{{ route('profile') }}">
                            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                        </a>
                        <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-left me-1"></i>Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
let savedThemeAuth = localStorage.getItem('theme') || 'auto';
document.addEventListener('DOMContentLoaded', function() {
    // Função para aplicar o tema
    function setTheme(theme) {
        const htmlElement = document.documentElement;

        if (theme === 'auto') {
            // Detecta preferência do sistema
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            htmlElement.setAttribute('data-bs-theme', systemTheme);
        } else {
            htmlElement.setAttribute('data-bs-theme', theme);
        }

        // Salva no localStorage
        localStorage.setItem('theme', theme);

        // Atualiza o radio button correspondente
        document.querySelectorAll('input[name="theme"]').forEach(radio => {
            radio.checked = radio.value === theme;
        });

        savedThemeAuth = theme; // Atualiza a variável global para SweetAlert2
    }

    setTheme(savedThemeAuth);

    // Adiciona evento de mudança nos radio buttons
    document.querySelectorAll('input[name="theme"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                setTheme(this.value);
            }
        });
    });

    // Monitora mudanças na preferência do sistema (para tema 'auto')
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        const currentTheme = localStorage.getItem('theme') || 'auto';
        if (currentTheme === 'auto') {
            const htmlElement = document.documentElement;
            htmlElement.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
        }
    });
});
</script>
