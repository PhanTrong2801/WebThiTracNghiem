<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Hệ thống thi trắc nghiệm trực tuyến lớn nhất Việt Nam">
        <meta name="author" content="pixelcave">
        
        <!-- Open Graph Meta -->
        <meta property="og:title" content="Quản lý đề thi trắc nghiệm">
        <meta property="og:site_name" content="Quản lý đề thi trắc nghiệm">
        <meta property="og:description" content="Hệ thống thi trắc nghiệm trực tuyến lớn nhất Việt Nam">
        <meta property="og:type" content="website">
        <meta property="og:url" content="">
        <meta property="og:image" content="">

        <!-- Icons -->
        <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('media/favicons/favicon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon-180x180.png') }}">
        <!-- END Icons -->

        <title inertia>{{ config('app.name', 'STU Test') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
        <link rel="stylesheet" href="{{ asset('js/plugins/flatpickr/flatpickr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('js/plugins/slick-carousel/slick.css') }}">
        <link rel="stylesheet" href="{{ asset('js/plugins/slick-carousel/slick-theme.css') }}">
        <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" id="css-main" href="{{ asset('css/dashmix.min.css') }}">
        <link rel="stylesheet" id="css-custom" href="{{ asset('css/custom.css') }}">

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        <script>
            // Nếu có localStorage dark-mode thì add class
            if (localStorage.getItem('dashmix_dark_mode') === 'true') {
                document.body.classList.add('dark-mode');
            }
        </script>
        
        @inertia

        <!-- Dashmix Core JS -->
        <!-- jQuery loaded here; dashmix.app.min.js is loaded dynamically by app.jsx after React mounts -->
        <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
        
        <!-- Page Plugins -->
        <script src="{{ asset('js/plugins/slick-carousel/slick.min.js') }}"></script>
        <script src="{{ asset('js/plugins/jquery-appear/jquery.appear.min.js') }}"></script>
    </body>
</html>
