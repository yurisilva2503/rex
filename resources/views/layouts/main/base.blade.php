<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        let savedThemeBase = localStorage.getItem('theme') || 'auto';
        let htmlElement = document.documentElement;
        if (savedThemeBase === 'auto') {
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            htmlElement.setAttribute('data-bs-theme', systemTheme);
        } else {
            htmlElement.setAttribute('data-bs-theme', savedThemeBase);
        }
    </script>

    <title>{{ isset($title) ? env('APP_NAME') . ' - ' . $title : env('APP_NAME') }}</title>
    <link rel="shortcut icon" href="{{ asset('/assets/img/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('/assets/css/extra.css') }}">
    <script src="{{ asset('/assets/js/bootstrap-icons.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/moment-2.29.4/jszip-3.10.1/dt-2.3.7/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/r-3.0.8/datatables.min.css"
        rel="stylesheet" integrity="sha384-L5dsz/jJPV3hmalRl0WJ7KJHPNOzyVkIbzXOfW4tnNAJz3jCRdXRzczz1bFJfGqa"
        crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"
        integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"
        integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n"
        crossorigin="anonymous"></script>
    <script
        src="https://cdn.datatables.net/v/bs5/jq-3.7.0/moment-2.29.4/jszip-3.10.1/dt-2.3.7/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/r-3.0.8/datatables.min.js"
        integrity="sha384-YdaS5BrOotRWo34Xp1rh2CTrdAEsA0PC7m/5FnT8aaoxnyOQcKPAqorkkweW8VZI"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/pt-br.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="{{ asset('/assets/js/extra.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-body">
    @if(auth()->check())
        @include('layouts.main.headerauth')
    @else
        @include('layouts.main.header')
    @endif
    @yield('content')
    @include('layouts.main.footer')
</body>

</html>
