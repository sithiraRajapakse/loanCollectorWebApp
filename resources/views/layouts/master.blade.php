<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <title> @yield('title') | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{config('common.description')}}" name="description"/>
    <meta content="{{config('common.developer.name')}}" name="author"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico')}}">
    @include('layouts.head')
</head>

@section('body')
@show
<body data-sidebar="dark">
<div id="preloader">
    <div id="status">
        <div class="spinner-chase">
            <div class="chase-dot"></div>
            <div class="chase-dot"></div>
            <div class="chase-dot"></div>
            <div class="chase-dot"></div>
            <div class="chase-dot"></div>
            <div class="chase-dot"></div>
        </div>
    </div>
</div>
<!-- Begin page -->
<div id="layout-wrapper">
@include('layouts.topbar')
@include('layouts.sidebar')
<!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <!-- Alerts -->
            @include('layouts.alerts')
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
        @include('layouts.footer')
    </div>
    <!-- end main content-->
</div>
<!-- END layout-wrapper -->

<!-- JAVASCRIPT -->
@include('layouts.footer-script')
</body>
</html>
