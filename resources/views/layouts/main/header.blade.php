<header class="main-header bg-body shadow-sm border-bottom p-2 px-1">
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center">
            <a href="{{ route('login') }}" class="col-md-3 text-decoration-none">
                <div class="d-flex">
                    <img src="{{ asset('/assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 50px;">
                    <p style="font-family: monospace;" class="fw-bold text-center m-0 fs-2 text-body">
                        {{ ucfirst(substr(env('APP_NAME'), 0, 1)) }}<span class="text-primary">{{ substr(env('APP_NAME'), 1) }}</span>
                    </p>
                </div>
            </a>
            <div class="col-md-6">
                <div class="d-flex justify-content-center gap-1 mb-1">
                    <a class="nav-link {{ request()->routeIs('login') ? 'active border-bottom border-primary' : '' }}"
                    href="{{ route('login') }}"><i class="bi bi-house me-1"></i>Página Inicial</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center justify-content-end gap-3 mb-1">
                    <!-- Seletor de tema (mesmo código) -->
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

                    <div class="vr"></div>

                    <!-- Botão de login existente -->
                    <a href="{{ route('login') }}">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>


<script>
let savedTheme = localStorage.getItem('theme') || 'auto';
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
        savedTheme = theme;
    }

    // Carrega o tema salvo ou usa 'auto' como padrão
    setTheme(savedTheme);

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
