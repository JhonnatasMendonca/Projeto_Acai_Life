<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="favicon icon" href="{{ asset('images/favicon.PNG') }}">
    <title>@yield('title')</title>


    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('style/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('style/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style/app.css') }}">
    <link rel="stylesheet" href="{{ asset('style/toastr.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('style/ag-grid.css') }}">
    <link rel="stylesheet" href="{{ asset('style/ag-theme-alpine.css') }}">

    <link rel="stylesheet" href="{{ asset('style/all.min.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>

    </style>

</head>

<body class="fundo_color">
    
    
    
    @unless (in_array(Route::currentRouteName(), ['login', 'recuperar-senha', 'logout', 'entrar', 'atualizar-senha']))
    @include('partials.header')
    @endunless
    
    <div class="" style="padding: 0 20px; color: #fff;">
        @yield('content')
    </div>
    
    @unless (in_array(Route::currentRouteName(), ['login', 'recuperar-senha', 'logout', 'entrar', 'atualizar-senha']))
    @include('partials.footer')
    @endunless
    
    
    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/flatpickr.js') }}"></script>
    <script src="{{ asset('js/pt.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/ag-grid-community.min.noStyle.js') }}"></script>
    <script src="{{ asset('js/apexcharts.js') }}"></script>
    <script src="{{ asset('js/jspdf.umd.min.js') }}"></script>
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
    
    



    <script type="module">
        $(document).ready(function() {
            @if (session('mensagem'))
                toastr.success("{{ session('mensagem') }}");
            @endif

            @if (session('erro'))
                toastr.error("{{ session('erro') }}");
            @endif
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>



</body>

</html>
