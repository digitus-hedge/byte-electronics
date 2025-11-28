<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Byte Electronics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Byte Electronics" name="description" />
    <meta content="Byte Electronics" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ url('assets/images/BYTE_LOGO.webp') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/quill/quill.core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"
        type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/libs/quill/quill.snow.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href={{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.5/tagify.min.css">
    @stack('styles')
    <style>
        .main-content .content {
            margin-top: 25px !important;
        }

        .page-content {
            padding: calc(85px - 33px) calc(22px / 2) 60px calc(22px / 2);
        }
    </style>
</head>

<body data-topbar="dark">
    <div id="layout-wrapper">
        @include('components.navbar')
        @include('components.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('components.footer')
        </div>
    </div>
    <div class="rightbar-overlay"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenujs/metismenujs.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
