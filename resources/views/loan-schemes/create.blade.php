@extends('layouts.master')

@section('title') Loan Schemes Management @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Loan Schemes @endslot
        @slot('li_1') Manage your loan schemes here @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="card d-none">
                <div class="card-header">
                    Create a Loan Scheme
                </div>
                <div class="card-body">
                    <form action="{{ route('loans.schemes.create') }}" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="type">Installment Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        @php
                                            $typesArray = [
                                                \App\Enums\SchemeType::DAILY => 'Daily Installments',
                                                \App\Enums\SchemeType::WEEKLY => 'Weekly Installments',
                                                \App\Enums\SchemeType::MONTHLY => 'Monthly Installments',
                                                \App\Enums\SchemeType::CUSTOM => 'Customized Installments',
                                            ];
                                        @endphp
                                        @foreach($typesArray as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="interest_rate">Interest Rate</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="interest_rate"
                                               name="interest_rate" value="0" min="0" max="100" step="0.1"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-sm-12">
            <div class="card-header">
                Loan Schemes List
            </div>
            <div class="card card-body">
                <table class="table table-bordered" id="schemes_table">
                    <colgroup>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Installment Type</th>
                        <th>Interest Rate</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($schemes as $scheme)
                        <tr>
                            <td>{{ $scheme->title }}</td>
                            <td>{{ ucwords(strtolower($scheme->type) . ' Installments') }}</td>
                            <td>{{ $scheme->interest_rate }}%</td>
                            <td>
                                <a href="{{ route('loans.schemes.edit', $scheme->id) }}" class="btn btn-sm btn-primary">
                                    <span class="fa fa-edit"></span> Edit
                                </a>

{{--                                <form action="{{ route('loans.schemes.delete', $scheme->id) }}" method="post" class="d-inline">--}}
{{--                                    @csrf--}}
{{--                                    <button type="submit" class="delete_scheme btn btn-sm btn-danger">--}}
{{--                                        <span class="fa fa-trash"></span> Delete--}}
{{--                                    </button>--}}
{{--                                </form>--}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No loan schemes available.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('styles-top')
    <link href="{{ URL::asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('script-bottom')
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script>
        $(function () {
            if($('#schemes_table tbody tr').length > 5) {
                $('#schemes_table').DataTable();
            }
        });

        $('.delete_scheme').click(function(e){
            return confirm('Are you sure you need to proceed with the deletion of this loan scheme entry?');
        })

    </script>
@endsection
