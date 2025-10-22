<!DOCTYPE html>

<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="IUEs/INSAM- Face Recognition Attendance System For Performing Daily Attendance">
    <meta name="author" content="IUEs/INSAM- Face Recognition Attendance System For Performing Daily Attendance">
    <meta name="keywords" content="attendance, HR, facial recognition, daily attendance, check in, check out, admin panel, flutter app">
    <title>IUEs/INSAM - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
    @yield('css')
</head>
<body>
@include('sweetalert::alert')
<div class="main-wrapper">

    <!-- partial:partials/_sidebar.html -->
    @include('include.sidebar')
    <!-- partial -->

    <div class="page-wrapper">

        <!-- partial:partials/_navbar.html -->
        @include('include.header')
        <!-- partial -->

        <div class="page-content">

            @include('include.page_header')
{{--            @if( !config('system.isVerified') && !config('system.isDemo'))--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-12 grid-margin stretch-card">--}}
{{--                        <marquee direction="left">--}}
{{--                            Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code--}}
{{--                        </marquee>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endif--}}
            @yield('content')

        </div>

        <!-- partial:partials/_footer.html -->
        @include('include.footer')
        <!-- partial -->

    </div>
</div>

<!-- core:js -->
<script src="{{ asset('assets/vendors/core/core.js') }}"></script>
<!-- endinject -->

<!-- inject:js -->
<script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/template.js') }}"></script>
<!-- endinject -->

<!-- End custom js for this page -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/delete_model.js') }}"></script>

@yield('js')
<?php Session::forget('sweet_alert'); ?>
</body>
</html>
