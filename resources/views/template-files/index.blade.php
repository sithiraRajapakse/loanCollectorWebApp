@extends('layouts.master')

@section('title') Dashboard @endsection

@section('content')


    @component('common-components.breadcrumb')
        @slot('title') Dashboard  @endslot
        @slot('li_1') Welcome to {{ config('app.name') }} @endslot
    @endcomponent

@endsection

@section('script')
    <!-- plugin js -->
    {{--        <script src="{{ URL::asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>--}}

    <!-- Calendar init -->
    {{--        <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>--}}
@endsection
