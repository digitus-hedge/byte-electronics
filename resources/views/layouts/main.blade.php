<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>Byte Electronics</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesdesign" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{url('assets/images/BYTE_LOGO.webp')}}">

        <!-- plugin css -->
        <link href="{{asset('assets/libs/jsvectormap/css/jsvectormap.min.css')}}" rel="stylesheet" type="text/css" />

        <!-- swiper css -->
        <link rel="stylesheet" href="{{asset('assets/libs/swiper/swiper-bundle.min.css')}}">

        <!-- Bootstrap Css -->
        <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
         <!-- quill css -->
         <link href="{{asset('assets/libs/quill/quill.core.css')}}" rel="stylesheet" type="text/css">
         <link href="{{asset('assets/libs/choices.js/public/assets/styles/choices.min.css')}}" rel="stylesheet" type="text/css">
         <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
         <link href="{{asset('assets/libs/quill/quill.snow.css')}}" rel="stylesheet" type="text/css">
         <link rel="stylesheet" href={{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}>
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.5/tagify.min.css">
        @stack('styles')

    </head>

    <body data-topbar="dark">


    <div id="layout-wrapper">

     @include('components.navbar')

       @include('components.sidebar')
       <div class="main-content" >

            <div class="page-content" style="margin-top:0px;">

                @yield('content')

            </div>

           @include('components.footer')
        </div>


    </div>
    <div class="rightbar-overlay"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@stack('scripts')
    <script src="{{asset('assets/js/app.js')}}"></script>
    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/metismenujs/metismenujs.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>

    {{-- <script src="{{asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script> --}}


    {{-- <script src="{{asset('assets/libs/jsvectormap/js/jsvectormap.min.js')}}"></script> --}}
    {{-- <script src="{{asset('assets/libs/jsvectormap/maps/world-merc.js')}}"></script> --}}


    <script src="{{asset('assets/libs/swiper/swiper-bundle.min.js')}}"></script>

    <script src="{{asset('assets/js/pages/dashboard.init.js')}}"></script>




    </body>

</html>
