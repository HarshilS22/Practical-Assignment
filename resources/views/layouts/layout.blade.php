<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('dist/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- End plugin css for this page -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('dist/assets/css/style.css') }}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset('dist/assets/images/favicon.png') }}"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
<div class="container-scroller">

    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
            <a class="navbar-brand brand-logo" href="#">
                <img src="{{ asset('dist/assets/images/logo.svg') }}" alt="logo"/>
            </a>
            <a class="navbar-brand brand-logo-mini" href="#">
                <img src="{{ asset('dist/assets/images/logo-mini.svg') }}" alt="logo"/>
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="mdi mdi-menu"></span>
            </button>

        </div>

    </nav>

    <div class="container-fluid page-body-wrapper">
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item nav-profile">
                    <a href="#" class="nav-link">
                        <div class="nav-profile-image">
                            <img src="{{ asset('dist/assets/images/faces/face1.jpg') }}" alt="profile"/>
                            <span class="login-status online"></span>
                        </div>
                        <div class="nav-profile-text d-flex flex-column">
                            <span class="font-weight-bold mb-2">{{ session('user_name') }}</span>
                            <span class="text-secondary text-small">{{ session('user_role') }}</span>

                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard">
                        <span class="menu-title">Dashboard</span>
                        <i class="mdi mdi-home menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tasks">
                        <span class="menu-title">Tasks</span>
                        <i class="mdi mdi-crosshairs-gps menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}">
                        <span class="menu-title">Logout</span>
                        <i class="mdi mdi-logout  menu-icon"></i>
                    </a>
                </li>
            </ul>
        </nav>

        @section('main-content')

        @show

        @section('scripts')
        @show
    </div>
</div>

<!-- plugins:js (contains jQuery) -->
{{--<script src="{{ asset('dist/assets/vendors/js/vendor.bundle.base.js') }}"></script>--}}


<!-- DataTables Core -->
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>

<!-- DataTables Responsive -->
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>


<!-- Plugin js -->
<script src="{{ asset('dist/assets/vendors/chart.js/chart.umd.js') }}"></script>
<script src="{{ asset('dist/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<!-- inject:js -->
<script src="{{ asset('dist/assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('dist/assets/js/misc.js') }}"></script>
<script src="{{ asset('dist/assets/js/settings.js') }}"></script>
<script src="{{ asset('dist/assets/js/todolist.js') }}"></script>
<script src="{{ asset('dist/assets/js/jquery.cookie.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom js -->
<script src="{{ asset('dist/assets/js/dashboard.js') }}"></script>
</body>

</html>
