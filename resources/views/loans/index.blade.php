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

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    Registered Loans
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="active_loans_table">
                            <colgroup>
                                <col style="width: 15%;">
                                <col style="width: 20%;">
                                <col style="width: 30%;">
                                <col style="width: 20%;">
                                <col style="width: 15%;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>Loan No.</th>
                                <th>Scheme</th>
                                <th>Customer</th>
                                <th>Period</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($loans as $loan)
                                @if (!empty($loan->customer))
                                    <tr>
                                        <td>{{ $loan->loan_number }}</td>
                                        <td>{{ $loan->scheme->title }}<br><small>Interest: {{ $loan->scheme->interest_rate }}%</small></td>
                                        <td><a href="{{ route('customers.edit', $loan->customer->id) }}">{{ $loan->customer->name }}</a><br><small>{{ $loan->customer->telephone }}</small></td>
                                        <td>From : {{ $loan->start_date }} <br>To : {{ $loan->end_date }}</td>
                                        <td><a class="btn btn-primary" href="{{ route('loan.installments.edit', $loan->id) }}">Edit Installments</a></td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5">Active loans will appear in here after registration.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

    {{-- create customer select modal --}}
    <div class="modal" tabindex="-1" id="customer-selection-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Select Customer</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="customer-selection-table" class="table table-bordered"
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Address</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-customer-select"
                                            data-customer="{{ $customer->id }}">Select
                                    </button>
                                </td>
                                <td>
                                    {{$customer->name}} ({{$customer->nic_no ?? 'N/A'}})
                                </td>
                                <td>
                                    <small>
                                        <strong>Address: </strong>{{$customer->address}}<br>
                                        <strong>Telephone : </strong>{{$customer->telephone ?? 'N/A'}} |
                                        <strong>Email : </strong>{{$customer->email ?? 'N/A'}}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No customer records found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--  end: create customer select modal  --}}
@endsection

@section('styles-top')
    <link href="{{ URL::asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/libs/toastr/toastr.min.css') }}">
@endsection

@section('script-bottom')
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/moment/moment-range.js') }}"></script>

    <script>
        window['moment-range'].extendMoment(moment);

        function attachInputMask() {
            $("#loan_amount").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: '',
                digits: 2,
                prefix: ''
            });
            $("#installment_amount").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: '',
                digits: 2,
                prefix: ''
            });
        }

        function getCustomerDetails(id, callback) {
            let req = {id: id};
            $.get('{{ route('customer.json') }}', req, function (res) {
                callback(res);
            });
        }

        function attachDatatable(table) {
            if ($(table + ' tbody tr').length > 5) {
                $(table).DataTable();
            }
        }

        // bind customer selection modal
        $('#customer-search-button').click(function () {
            $('#customer-selection-modal').modal();
        });

        $(document).on('click', '.btn-customer-select', function (e) {
            let customerId = $(this).data('customer');

            getCustomerDetails(customerId, function (res) {
                if (res.id === undefined) {
                    return false;
                }

                let ht = `${res.name} (${res.nic_no}) - ${res.telephone}`;
                $('#customer-description').text(ht);
                $('#customer_id').val(res.id);
            });

            $('#customer-selection-modal').modal('hide');
        });

        function initStartDate() {
            $('#start_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            }).on('changeDate', function (e) {
                if (e.date) {
                    initEndDate(e.date);
                }
            });
        }

        function initEndDate(startDate) {
            // clear datepicker
            $('#end_date').datepicker('update', '').datepicker('destroy');

            // init datepicker again
            $('#end_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                startDate: startDate
            }).datepicker('show');
        }

        function initInterestRateDisplay() {
            $('#scheme_id').change(function () {
                showInterestRateForSchemeId();
            });
        }

        function showInterestRateForSchemeId() {
            let scheme = getInstallmentData();
            $('#interest_rate').val(scheme.interest_rate);
            calculateInstallment();
        }

        function initInstallmentAmountCalculation() {
            $('#start_date, #end_date, #loan_amount, #interest_rate, #installment_amount, #last_installment_type').change(function (e) {
                calculateInstallment();
            });
        }

        function getInstallmentData() {
            let schemeOption = $('#scheme_id option:selected');
            return {
                id: schemeOption.data('scheme_id'),
                type: schemeOption.data('scheme_type'),
                interest_rate: schemeOption.data('scheme_interest_rate')
            };
        }

        function getInstallmentInterval() {
            let scheme = getInstallmentData();
            let interval = 'days';

            switch (scheme.type) {
                case '{{ \App\Enums\SchemeType::DAILY }}': {
                    interval = 'days';
                }
                    break;
                case '{{ \App\Enums\SchemeType::WEEKLY }}': {
                    interval = 'weeks';
                }
                    break;
                case '{{ \App\Enums\SchemeType::MONTHLY }}': {
                    interval = 'months';
                }
                    break;
            }
            return interval;
        }

        function calculateInstallment() {
            // calculate days
            let _st = $('#start_date').datepicker('getFormattedDate');
            let _ed = $('#end_date').datepicker('getFormattedDate');
            let startDate = moment(_st);
            let endDate = moment(_ed);
            let installmentType = getInstallmentInterval();
            let installmentsCount = endDate.diff(startDate, installmentType) + 1;
            let loanAmount = Number($('#loan_amount').val());
            let interestRate = Number($('#interest_rate').val());

            // set the installments count
            $('#no_of_installments').val(installmentsCount);

            // if the amounts are valid, show the
            if (!isNaN(loanAmount) && !isNaN(interestRate) && !isNaN(installmentsCount)) {
                let interestAmount = (loanAmount / 100) * interestRate;
                let installmentAmount = (loanAmount + interestAmount) / installmentsCount;

                let _customAmount = Number($('#installment_amount').val());
                if ((_customAmount != installmentAmount) && (_customAmount > 0) && !isNaN(_customAmount)) {
                    installmentAmount = _customAmount;
                } else {
                    $('#installment_amount').val(Number(installmentAmount).toFixed(2));
                }

                // show installments preview table
                $('#installments_schedule').val('');

                let scheme = getInstallmentData();
                if (scheme.type === '{{ \App\Enums\SchemeType::CUSTOM }}') {
                    showCustomMessageTable();
                } else {
                    // other
                    showInstallmentsPreview({
                        start_date: startDate,
                        end_date: endDate,
                        loan_amount: loanAmount,
                        interest_amount: interestAmount,
                        installments_count: installmentsCount,
                        installment_amount: installmentAmount
                    });
                }
            }
        }

        function showCustomMessageTable() {
            let body = $('#installmentsPreview tbody');
            let footer = $('#installmentsPreview tfoot');
            body.html('');
            footer.html('');

            let bodyHtml = `<tr><td colspan="3">You can add installments on the next step.</td></tr>`;
            body.html(bodyHtml);
        }

        function showInstallmentsPreview(data) {
            const interval = getInstallmentInterval();
            const lastInstallmentType = $('#last_installment_type').val();

            const range = moment.range(
                moment(data.start_date, 'YYYY-MM-DD'),
                moment(data.end_date, 'YYYY-MM-DD')
            );

            let installmentsCount = data.installments_count;
            let installment_amount = data.installment_amount;
            let deductingTotal = data.loan_amount + data.interest_amount;

            let previewItems = {};
            let installmentNumber = 1; // start from 1

            let total = 0;
            for (let month of range.by(interval)) {
                let thisInstallment = (deductingTotal > installment_amount) ? installment_amount : deductingTotal;

                if (installmentNumber <= installmentsCount && deductingTotal > 0) {
                    // check if the last installment
                    if (installmentNumber == installmentsCount) {
                        if (lastInstallmentType == '{{ \App\Enums\LastInstallmentType::ADD_TO_PREVIOUS }}') {
                            let lastAmount = previewItems[installmentNumber - 1].amount;
                            previewItems[installmentNumber - 1].amount = lastAmount + thisInstallment;
                        } else {
                            // if the balance is greater than zero
                            previewItems[installmentNumber] = {
                                number: installmentNumber,
                                dueDate: month.format('YYYY-MM-DD'),
                                amount: thisInstallment
                            };
                        }
                    } else {
                        // if no the last installment
                        previewItems[installmentNumber] = {
                            number: installmentNumber,
                            dueDate: month.format('YYYY-MM-DD'),
                            amount: thisInstallment
                        };
                    }

                    // deduct this installment from the total amount
                    deductingTotal = (deductingTotal - thisInstallment);

                    total = total + thisInstallment;

                    // increment installment number
                    installmentNumber++;
                }
            }

            $('#installments_schedule').val(JSON.stringify(previewItems));

            let body = $('#installmentsPreview tbody');
            let footer = $('#installmentsPreview tfoot');
            body.html('');
            footer.html('');

            // clear table
            let ht = '';
            if (Object.keys(previewItems).length > 0) {
                $.each(previewItems, function (i, inst) {
                    ht += `<tr><td class="text-right">Installment ${inst.number}</td>
                           <td class="text-left">${inst.dueDate}</td>
                           <td class="text-right">Rs. ${Number(inst.amount).toFixed(2)}</td></tr>`;
                });
            } else {
                ht += `<td colspan="3">Please <strong>Register Loan</strong> to add customized installments.</td>`;
            }

            body.html(ht);
            footer.html(`<tr><th colspan="2" class="text-right">Total</th><th class="text-right">${total.toFixed(2)}</th></tr>`);
        }

        $('#createLoanForm').submit(function (e) {
            let customerId = $('#customer_id').val();
            if (customerId == '') {
                alert('Please select a customer.');
                return false;
            }
            return true;
        });

        $(function () {
            initStartDate();
            attachInputMask();
            attachDatatable('#customer-selection-table');
            attachDatatable('#active_loans_table');
            initInstallmentAmountCalculation();
            initInterestRateDisplay();
            showInterestRateForSchemeId();
        });
    </script>
@endsection
