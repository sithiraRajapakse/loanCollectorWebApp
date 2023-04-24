@extends('layouts.master')

@section('title') Reports @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Reports @endslot
        @slot('li_1') Display and print data summary and detailed reports @endslot
    @endcomponent

    <div class="d-flex align-items-stretch">
        <div class="row">
            <div class="col-lg-3">
                <div class="card border border-primary">
                    <div class="card-body h-200">
                        <h3 class="card-title">Customers List</h3>
                        <p>A list of customers in the alphabetical order.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('reports.customers', \App\Enums\ReportPrintType::PRINT) }}" class="btn btn-primary btn-block btn-sm" target="_blank">
                            <span class="bx bxs-printer"></span> Display Report (Printable)
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card border border-primary">
                    <div class="card-body h-200">
                        <h3 class="card-title">Loans List Report</h3>
                        <p>A list of loans in the alphabetical order.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('reports.loan-list', \App\Enums\ReportPrintType::PRINT) }}" class="btn btn-primary btn-block btn-sm" target="_blank">
                            <span class="bx bxs-printer"></span> Display Report (Printable)
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card border border-primary">
                    <form action="{{ route('reports.loan-list-scheme', \App\Enums\ReportPrintType::PRINT) }}" method="get" target="_blank">
                        @csrf
                        <div class="card-body h-200">
                            <h3 class="card-title">Loans By Scheme Report</h3>
                            <p>A list of loans in the alphabetical order by the selected scheme.</p>
                            <div class="form-group">
                                <select name="scheme_id" id="scheme_id" class="form-control">
                                    @foreach($schemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block btn-sm" target="_blank">
                                <span class="bx bxs-printer"></span> Display Report (Printable)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card border border-success">
                    <form action="" method="post">
                        @csrf
                        <div class="card-body h-200">
                            <h3 class="card-title">Payments Collections Report</h3>
                            <p>Loan payments collection detailed report for the selected date. Select a date below.</p>
                            <div class="form-group">
                                <input type="date" name="selected_date" id="selected_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="" class="btn btn-primary btn-block btn-sm" target="_blank">
                                <span class="bx bxs-printer"></span> Display Report (Printable)
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-3">

            </div>
        </div>
    </div>
@endsection

@section('styles-top')
    <style>
        .h-200 {
            height: 200px;
            overflow-y: hidden;
            overflow-x: auto;
        }
    </style>
@endsection

@section('script-bottom')
@endsection
