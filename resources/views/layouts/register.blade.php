<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin</title>

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('dist/assets/css/style.css') }}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset('dist/assets/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">

                            <h4>New here?</h4>
                            <h6 class="font-weight-light">
                                Signing up is easy. It only takes a few steps
                            </h6>

                            <form class="pt-3" method="POST" action="{{ route('register') }}"  autocomplete="off">
                                @csrf

                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" name="name" autocomplete="off"
                                        placeholder="Username*" required>
                                </div>

                                <div class="form-group">
                                    <input type="email" required class="form-control form-control-lg" name="email" autocomplete="off"
                                        placeholder="Email*">
                                </div>

                                <div class="form-group">
                                    <input type="password" required class="form-control form-control-lg" name="password"
                                        placeholder="Password*">
                                </div>
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit"
                                        class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                        SIGN UP
                                    </button>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="text-primary">Login</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <!-- plugins:js -->
    <script src="{{ asset('dist/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->

    <!-- inject:js -->
    <script src="{{ asset('dist/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('dist/assets/js/misc.js') }}"></script>
    <script src="{{ asset('dist/assets/js/settings.js') }}"></script>
    <script src="{{ asset('dist/assets/js/todolist.js') }}"></script>
    <script src="{{ asset('dist/assets/js/jquery.cookie.js') }}"></script>
    <!-- endinject -->

</body>

</html>
