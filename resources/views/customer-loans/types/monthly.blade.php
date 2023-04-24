@extends('layouts.master')

@section('title') Monthly Loan @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Register New Monthly Loan @endslot
        @slot('li_1') @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-5">
            <div class="card border border-success">
                <div class="card-body">
                    <p>Fields marked with <span style="color: #ff0000;">*</span> are required.</p>

                    <form action="{{ route('customer-loans.monthly.create') }}" method="post">
                        @csrf
                        <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme->id }}"/>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="customer_id" required>Customer</label>
                                    <div class="controls">
                                        <select name="customer_id" id="customer_id" class="form-control select2ize">
                                            @foreach($customerList as $customer)
                                                <option
                                                    value="{{ $customer->id }}" {{ (old('customer_id') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="loan_amount" required>Expected Loan Amount</label>
                                    <div class="controls">
                                        <input type="number" name="loan_amount" id="loan_amount"
                                               class="form-control trigger-query-installments" min="1"
                                               step="1" placeholder="0.00" value="{{ old('loan_amount') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="interest_rate" required>Interest Rate</label>
                                    <div class="controls">
                                        <input type="number" name="interest_rate" id="interest_rate"
                                               class="form-control trigger-query-installments"
                                               min="1" step="1" placeholder="0.0"
                                               value="{{ old('interest_rate', $scheme->interest_rate) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="start_date" required>Start Date</label>
                                    <div class="controls">
                                        <input type="text" name="start_date" id="start_date"
                                               class="form-control trigger-query-installments"
                                               value="{{ old('start_date') }}" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="send_reg" name="send_reg" class="btn btn-success" >
                                Register Loan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border border-primary">
                <div class="card-header bg-primary text-white">
                    Customer Details
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <colgroup>
                            <col style="width: 20%;">
                            <col style="width: 80%;">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td>Name</td>
                            <td customer-placeholder-name>Customer Name</td>
                        </tr>
                        <tr>
                            <td>Telephone No.</td>
                            <td customer-placeholder-telephone>1234567890</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td customer-placeholder-address>Address line goes down here</td>
                        </tr>
                        <tr>
                            <td>Location</td>
                            <td customer-placeholder-location>Location Details</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles-top')
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/toastr/toastr.min.css') }}">
@endsection

@section('script-bottom')
    <script src="{{ asset('assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/moment/moment-range.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
    <script>
        function queryCustomerInformation() {
            let customerId = $('#customer_id').val();
            $.get('{{ route('customer.json') }}', {id: customerId}, function (res) {
                if (res.id) {
                    $('td[customer-placeholder-name]').html(res.name);
                    $('td[customer-placeholder-telephone]').html(res.telephone);
                    $('td[customer-placeholder-address]').html(res.address);
                    $('td[customer-placeholder-location]').html(res.location);
                } else {
                    toastr.error('Cannot retrieve customer information. Please refresh page and try again.');
                }
            }, 'json');
        }

        $('#customer_id').change(function () {
            queryCustomerInformation();
        });

        $(function () {
            $('.select2ize').select2();
            queryCustomerInformation();

            $('#start_date').datepicker({
                format: 'yyyy-mm-dd',
                datesDisabled: {!! $holidays->pluck('date')->toJson() !!}
            });
            $('#end_date').datepicker({
                format: 'yyyy-mm-dd',
                datesDisabled: {!! $holidays->pluck('date')->toJson() !!}
            });
        });
    </script>
@endsection
