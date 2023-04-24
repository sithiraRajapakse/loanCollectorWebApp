@extends('layouts.master')

@section('title') Loan Schemes Management @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Loan Schemes @endslot
        @slot('li_1') Manage your loan schemes here @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    Edit Loan Scheme Details
                </div>
                <div class="card-body">
                    <form action="{{ route('loans.schemes.update', $scheme->id) }}" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required
                                           value="{{ old('title', $scheme->title) }}" readonly />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="type">Installment Type</label>
                                    <select name="type" id="type" class="form-control" required readonly>
                                        @php
                                            $typesArray = [
                                                \App\Enums\SchemeType::DAILY => 'Daily Installments',
                                                \App\Enums\SchemeType::WEEKLY => 'Weekly Installments',
                                                \App\Enums\SchemeType::MONTHLY => 'Monthly Installments',
                                                \App\Enums\SchemeType::CUSTOM => 'Customized Installments',
                                            ];
                                        @endphp
                                        <option value="{{ $scheme->type }}">{{ $typesArray[$scheme->type] }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="interest_rate">Interest Rate</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="interest_rate"
                                               name="interest_rate" min="0" max="100" step="0.1"
                                        value="{{ old('interest_rate', $scheme->interest_rate) }}" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles-top')
@endsection

@section('script-bottom')
@endsection
