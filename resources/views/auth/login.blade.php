<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="DigitalHR- Face Recognition Attendance System For Performing Daily Attendance">
    <meta name="author" content="DigitalHR- Face Recognition Attendance System For Performing Daily Attendance">
    <meta name="keywords"
          content="attendance, HR, facial recognition, daily attendance, check in, check out, admin panel, flutter app">

    <title>Digital HR - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css')}}">
    <!-- endinject -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}"/>
</head>
<body>
<div class="main-wrapper">
    @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])
    <div class="page-wrapper full-page">
        <div class="page-content d-flex align-items-center justify-content-center">
            <div class="row w-100 mx-0 auth-page">
                <div class="col-md-8 col-xl-4 mx-auto">
                    <div class="card p-5 text-center">
                        <div class="d-inline-block">
                            @if($_companyDetail && $_companyDetail->image)
                                <img class="w-25 rounded" src="{{asset($_companyDetail->image_path)}}">
                            @else
                                <img class="w-25 rounded" src="{{asset('assets/logo.png')}}">
                            @endif
                        </div>
                        <div class="auth-form-wrapper pt-4 pb-0">
                            <h3><a href="https://cninfotech.com/" class="noble-ui-logo d-block mb-2" target="_blank">Digital <span> HR</span></a></h3>
                            <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>
                            @if ($errors->has('login_error'))
                                <span class="text-danger">
                                            <strong>{{ $errors->first('login_error') }}</strong>
                                        </span>
                            @endif
                            @if ($errors->has('login_success'))
                                <span class="text-success">
                                            <strong>{{ $errors->first('login_success') }}</strong>
                                        </span>
                            @endif
                            <form class="forms-sample" action="{{route('admin.authenticate')}}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username/Email</label>
                                    <input type="text"
                                           class="form-control @error('username') is-invalid @enderror "
                                           id="username" name="username"
                                           placeholder="Username/Email" value="{{ old('username') }}"
                                           autofocus required>
                                    @if ($errors->has('username'))
                                        <span class="text-danger">
                                                    <strong>{{ $errors->first('username') }}</strong>
                                                </span>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" name="password" required
                                           class="form-control @error('password') is-invalid @enderror"
                                           autocomplete="current-password">
                                    @if ($errors->has('password'))
                                        <span class="text-danger">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                    @endif
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0 text-white">
                                        Login
                                    </button>
                                        <a class="btn btn-link" href="{{ route('admin.passwordReset') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- core:js -->
<script src="{{ asset('assets/vendors/core/core.js')}}"></script>
<!-- endinject -->

<!-- inject:js -->
<script src="{{ asset('assets/vendors/feather-icons/feather.min.js')}}"></script>
<script src="{{ asset('assets/js/template.js')}}"></script>
<!-- endinject -->

</body>
</html>
