@extends('layouts.master')

@section('title') Customer Loans Management @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Customer Loans @endslot
        @slot('li_1') Manage customer loans here @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            {{--  List all current registered loans here  --}}
        </div>
        <div class="col-lg-3">
            <div class="card card-body border border-primary">
                <h3 class="card-text">Daily Loan</h3>
                <a href="{{ route('customer-loans.daily') }}" class="btn btn-primary btn-block">Register New</a>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-body border border-success">
                <h3 class="card-text">Weekly Loan</h3>
                <a href="{{ route('customer-loans.weekly') }}" class="btn btn-success btn-block">Register New</a>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-body border border-info">
                <h3 class="card-text">Monthly Loan</h3>
                <a href="{{ route('customer-loans.monthly') }}" class="btn btn-info btn-block">Register New</a>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-body border border-secondary">
                <h3 class="card-text">Bi-Weekly Loan</h3>
                <a href="{{ route('customer-loans.bi-weekly') }}" class="btn btn-secondary btn-block">Register New</a>
            </div>
        </div>
    </div>

@endsection

@section('styles-top')
@endsection

@section('script-bottom')
@endsection
