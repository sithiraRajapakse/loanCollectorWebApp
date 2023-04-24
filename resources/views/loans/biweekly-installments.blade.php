@extends('layouts.master')

@section('title') Customer Loans Management @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Installments @endslot
        @slot('li_1') Customize and accept payments for the bi-weekly installment loans. @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-body">
                <h3 class="card-title">Loan Details</h3>
                <table class="table table-bordered table-condensed table-striped">
                    <colgroup>
                        <col style="width: 40%;">
                        <col style="width: 60%;">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>Loan Number</td>
                        <td data-fill="loan_number" class="text-right">{{ $loan->loan_number }}</td>
                    </tr>
                    <tr>
                        <td>Loan Scheme</td>
                        <td data-fill="loan_scheme" class="text-right">{{ ucfirst_forced($loan->scheme->type) }}</td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td class="text-right" data-fill="customer">
                            <a href="{{ route('customers.edit', $loan->customer->id) }}">{{ $loan->customer->name }}</a>
                            (<small>{{ $loan->customer->telephone }}</small>)
                        </td>
                    </tr>
                    <tr>
                        <td>Duration</td>
                        <td class="text-right" data-fill="duration">
                            <strong>From:</strong> {{ $loan->start_date }} <strong>To:</strong> {{ $loan->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <td>Interest Rate</td>
                        <td class="text-right text-primary" data-fill="interest_rate">{{ $loan->interest_rate }}%</td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td class="text-right text-primary" data-fill="amount">
                            Rs. {{ number_format($loan->loan_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Interest</td>
                        <td class="text-right text-primary" data-fill="interest_total">
                            Rs. {{ number_format($loan->interest_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        @php
                            $loanTotal = $loan->loan_amount + $loan->interest_total
                        @endphp
                        <td class="text-right text-primary" data-fill="loan_total" style="font-weight: bold">
                            Rs. {{  number_format($loanTotal, 2)  }}</td>
                    </tr>
                    @php
                        $installments = $loan->loanInstallments;
                        $totalPaid = $installments->pluck('paid_amount')->sum();
                        $totalInstallmentsAmountPaid = $installments->pluck('installment_amount')->sum();
                        $totalInterestAmountPaid = $installments->pluck('interest_amount')->sum();
                        $totalArrearsAmountPaid = $installments->pluck('arrears_amount')->sum();
                    @endphp
                    <tr>
                        <td>Total Paid</td>
                        @php
                            $totalPaid = $loan->loanInstallments->where('paid_at', '!=',  null)->sum('paid_amount')
                        @endphp
                        <td class="text-right text-success" data-fill="total_paid">
                            Rs. {{ number_format($totalPaid, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Arrears</td>
                        <td class="text-right text-danger" data-fill="total_arrears" style="font-weight: bold">
                            Rs. {{ number_format($loan->arrearsTotal(), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Loan Due Amount</td>
                        <td class="text-right text-danger" data-fill="due_amount" style="font-weight: bold">
                            Rs. {{ number_format($loanTotal - $totalInstallmentsAmountPaid, 2) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- TODO: Following block should only be displayed if the permissions are there for the logged in user.
             TODO: To be implemented after the laratrust implementation to the user module. --}}
            {{--            <div class="card">--}}
            {{--                <div class="card-header bg-soft-warning border-warning">--}}
            {{--                    <strong>More Actions</strong>--}}
            {{--                </div>--}}
            {{--                <div class="card-body">--}}
            {{--                    <button class="btn btn-lg btn-outline-primary" id="btn_open_accept_payment_modal"><span--}}
            {{--                            class="fa fa-money-bill"></span> Accept Payment--}}
            {{--                    </button>--}}
            {{--                    <button class="btn btn-lg btn-outline-danger" id="btn_remove_last_installment"><span--}}
            {{--                            class="fa fa-trash-alt"></span> Remove Last Installment--}}
            {{--                    </button>--}}
            {{--                </div>--}}
            {{--            </div>--}}
        </div>
        <div class="col-lg-8">

            <div class="card">
                <div class="card-header border-info bg-info text-white">
                    Installments
                </div>
                <div class="card-body">
                    <table class="table table-striped table-condensed mt-2" id="installments_table">
                        <colgroup>
                            <col style="width: 5%;">
                            <col style="width: 17%;">
                            <col style="width: 15%;">
                            <col style="width: 17%;">
                            <col style="width: 17%;">
                            <col style="width: 17%;">
                            <col style="width: 12%;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th class="text-right">Status</th>
                            <th class="text-right">Paid</th>
                            <th class="text-right">Installment</th>
                            <th class="text-right">Interest</th>
                            <th class="text-right">Arrears</th>
                            {{--                            <th>Balance</th>--}}
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $hasArrears = false
                        @endphp
                        @forelse($installments as $installment)
                            @php
                                $hasArrears = $installment->arrears_amount > 0
                            @endphp
                            <tr>
                                <td>{{ $installment->index_number }}</td>
                                <td>{{ $installment->due_date }}</td>
                                <td class="text-right">
                                    <strong
                                        class="{{ $installment->paid_at ? 'text-success' : 'text-danger' }}">{{ $installment->paid_at ? 'Paid' : 'Unpaid' }}</strong>
                                </td>
                                <td class="text-right text-success">{{ number_format($installment->paid_amount, 2) }}</td>
                                <td class="text-right">{{ number_format($installment->installment_amount, 2) }}</td>
                                <td class="text-right">{{ number_format($installment->interest_amount, 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($installment->arrears_amount, 2) }}</td>
                                {{--                                <td class="text-right text-info">{{ number_format(($installment->installment_amount - $installment->paid_amount), 2) }}</td>--}}
                                <td class="text-right">
                                    @if(!$installment->paid_at)
                                        <button class="btn btn-success btn-sm instbtn_pay_installment"
                                                data-installment_id="{{ $installment->id }}">
                                            <span class="fa fa-cash-register"></span>
                                        </button>
                                    @else
                                        <button class="btn btn-danger btn-sm instbtn_cancell_installment_payment"
                                                data-installment_id="{{ $installment->id }}">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No installments added yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot class="bg-light border-light">
                        @if(!empty($installments))
                            <tr style="font-weight: bold">
                                <td colspan="3">Total</td>
                                <td class="text-right text-success">{{ number_format( $installments->pluck('paid_amount')->sum() ,2) }}</td>
                                <td class="text-right text-info">{{ number_format( $installments->pluck('installment_amount')->sum() ,2) }}</td>
                                <td class="text-right text-info">{{ number_format( $installments->pluck('interest_amount')->sum() ,2) }}</td>
                                <td class="text-right text-danger">{{ number_format( $installments->pluck('arrears_amount')->sum() ,2) }}</td>
                                <td colspan="3"></td>
                            </tr>
                        @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--  Payment Modal  --}}
    <div class="modal" tabindex="-1" id="payment_modal">
        <div class="modal-dialog">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Pay the Installment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pm_installment_id" id="pm_installment_id" value="">
                    <div id="pm_content" class="pm_content"></div>
                    <div class="form-group">
                        <label for="pm_payment_amount">Paid Amount: </label>
                        <input type="text" class="form-control" id="pm_payment_amount" placeholder="0.00" value="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btn_pay_installment">Make Payment</button>
                </div>
            </div>
        </div>
    </div>
    {{--  Payment Modal:end  --}}
@endsection

@section('styles-top')
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/libs/toastr/toastr.min.css') }}">
@endsection

@section('script-bottom')
    <script src="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootbox/bootbox.min.js') }}"></script>
    <script>
        function init() {
            // init datepicker again
            $('#installment_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                startDate: '{{ $loan->start_date }}',
                endDate: '{{ $loan->end_date }}',
                datesDisabled: {!! $disablingDates->toJson() !!}
            });
            $("#installment_amount").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: '',
                digits: 2,
                prefix: ''
            });
            $('#payment_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                startDate: '{{ $loan->start_date }}',
                datesDisabled: {!! $disablingDates->toJson() !!}
            });
        }

        function disableDatePickerDates(dates) {
            $('#installment_date').datepicker('setDatesDisabled', dates).datepicker('update', '');
        }

        function addInstallment() {
            let loanId = $('#loan_id').val();
            let installmentDate = $('#installment_date').val();
            let installmentAmount = $('#installment_amount').val();

            let req = {
                loan_id: loanId,
                installment_date: installmentDate,
                installment_amount: installmentAmount
            };

            $.post('{{ route('loan.installments.add', $loan->id) }}', req, function (res) {
                if (res.status === 'success') {
                    toastr.success(res.data, 'Success');
                } else {
                    toastr.error('Failed to add installment. Please try again.', 'Failure');
                }
                loadInstallments();
            }, 'json');
        }

        function loadInstallments() {
            // empty container
            let tbody = $('#installments_table tbody');
            let tfoot = $('#installments_table tfoot');
            $.get('{{ route('loan.installments.list', $loan->id) }}', function (res) {
                let bht = '';
                let total = 0.0;
                let paid = 0.0;
                let arrears = 0.0;
                let datesList = [];
                if (res.status === 'success') {
                    $.each(res.data, function (i, x) {
                        const isUnpaid = x.paid_at == null;
                        const balance = Number(x.installment_amount) - Number(x.paid_amount);
                        bht += `<tr><td>${x.index_number}</td>
                        <td>${x.due_date}</td>
                        <td class="text-right"><strong class="${isUnpaid ? 'text-danger' : 'text-success'}">
                            ${isUnpaid ? 'Unpaid' : 'Paid'}</strong></td>
                        <td class="text-right text-success">${Number(x.installment_amount).toFixed(2)}</td>
                        <td class="text-right text-danger">${Number(x.paid_amount).toFixed(2)}</td>
                        <td class="text-right text-info">${Number(x.arrears_amount).toFixed(2)}</td>`;
                        // <td class="text-right">${balance.toFixed(2)}</td>
                        bht += `<td class="text-right">`;
                        if (isUnpaid) {
                            bht += `<button class="btn btn-success btn-sm instbtn_pay_installment" data-installment_id="${x.id}">
                                <span class="fa fa-cash-register"></span>
                            </button>`;
                        } else {
                            bht += `<button class="btn btn-danger btn-sm instbtn_cancell_installment_payment" data-installment_id="${x.id}">
                                <span class="fa fa-trash"></span>
                            </button>`;
                        }
                        bht += `</td></tr>`;

                        total += Number(x.installment_amount);
                        paid += Number(x.paid_amount);
                        arrears += Number(x.arrears_amount);
                        datesList.push(x.due_date);
                    });
                } else {
                    bht = `<tr><td colspan="8">No installments added yet.</td></tr>`;
                }

                tbody.html(bht);
                tfoot.html(`<tr style="font-weight: bold"><td colspan="3">Total</td>
                        <td class="text-right">${total.toFixed(2)}</td>
                        <td class="text-right text-success">${paid.toFixed(2)}</td>
                        <td class="text-right text-danger">${arrears.toFixed(2)}</td>
                        <td colspan="2"></td>
                        </tr>`);
                disableDatePickerDates(datesList);
            }, 'json');
        }

        function deleteLastInstallment() {
            $.post('{{ route('loan.installment.delete') }}', {id: '{{$loan->id}}'}, function (res) {
                if (res.status == 'success') {
                    toastr.success('Last installment for the loan deleted successfully.', 'Last Installment Deleted');
                } else {
                    toastr.error(res.data, 'Delete Failed');
                }
                loadInstallments();
            }, 'json');
        }

        $('#add_installment_form').submit(function (e) {
            e.preventDefault();
            addInstallment();
        });

        $(document).on('click', '#btn_remove_last_installment', function (e) {
            bootbox.confirm({
                size: "small",
                message: "Are you sure you need to delete the last installment of this loan?",
                callback: function (result) {
                    if (result) {
                        deleteLastInstallment();
                    }
                }
            });
        });

        $(document).on('click', '#btn_open_accept_payment_modal', function (e) {
            let loanId = '{{ $loan->id }}';

            let data = {loan_id: loanId};
            $.get('{{ route('loan.installment.payable-installment') }}', data, function (res) {
                if (res.status === 'success') {
                    showInstallmentPaymentModal(res.data);
                } else {
                    toastr.error(res.data, 'No Payable Installments');
                }
            }, 'json');

        });

        // show the payment modal accept payment for a selected installment
        $(document).on('click', '.instbtn_pay_installment', function (e) {
            let loanId = '{{ $loan->id }}';
            let instId = $(this).data('installment_id');

            let data = {loan_id: loanId, installment_id: instId};
            $.get('{{ route('loan.installment.installment-details') }}', data, function (res) {
                if (res.status === 'success') {
                    showInstallmentPaymentModal(res.data);
                } else {
                    toastr.error(res.data, 'System cannot accept payments for this installment at the moment. Please try again later.');
                }
            }, 'json');
        });

        // show the payment modal for payment reversal for a selected installment
        $(document).on('click', '.instbtn_cancell_installment_payment', function (e) {
            let installment_id = $(this).data('installment_id');
            bootbox.confirm({
                size: "small",
                message: "Are you sure you need to reverse the payment for this installment?",
                callback: function (result) {
                    if (result) {
                        reverseInstallmentPayment(installment_id);
                    }
                }
            });
        });

        function reverseInstallmentPayment(installmentId) {
            let data = {
                installment_id: installmentId
            };
            $.post('{{ route('loan.installment.payment.reverse') }}', data, function (res) {

            }, 'json');
        }

        function showInstallmentPaymentModal(data) {
            let installment = data.installment;
            let arrearsTotal = Number(data.arrears_total);
            let installment_amount = Number(installment.installment_amount);
            let totalPayable = installment_amount + arrearsTotal;
            // set stuff in the container for modal
            let payment_template = `<p>You are about to set the payment for <strong>Installment <span class="pm_installment">${installment.index_number}</span></strong></p>
                    <p>Installment amount is <strong>Rs. <span class="pm_installment_amount">${installment_amount}</span></strong></p>
                    <p>Due date is ${installment.due_date}</p>
                    <p>This installment : ${installment_amount.toFixed(2)}</p>
                    <p>Arrears as of now : ${arrearsTotal.toFixed(2)}</p>
                    <p>Total Payable (Installment + Arrears Total) : ${totalPayable.toFixed(2)}</p>`;
            $('#pm_payment_amount').val(totalPayable.toFixed(2));
            $('#payment_modal').find('#pm_installment_id').val(installment.id);
            $('#payment_modal').find('#pm_content').html(payment_template);
            $('#payment_modal').modal({backdrop: 'static'}).modal('show');
        }

        function resetPaymentModal() {
            $('#pm_payment_amount').val('');
            $('#payment_modal').find('#pm_installment_id').val('');
            $('#payment_modal').find('#pm_content').html('');
            $('#payment_modal').modal('hide');
        }

        $('#btn_pay_installment').click(function (e) {
            let linid = $('#pm_installment_id').val();
            let inpayamnt = $('#pm_payment_amount').val();
            let requestData = {
                installment_id: linid,
                paid_amount: inpayamnt,
            };

            $.post('{{ route('loan.installment.payment') }}', requestData, (response) => {
                if (response.status == 'success') {
                    // success
                    toastr.success(response.data, 'Payment Success');
                    getLoanDetailsJson();
                } else {
                    // error
                    toastr.error(response.data, 'Payment Failed');
                }
                resetPaymentModal();
                loadInstallments();
            }, 'json');
        });

        function getLoanDetailsJson() {
            $.get('{{ route('customer-loans.details-loan', $loan->id) }}', {}, function (res) {
                if (res.status == 'success') {
                    $('[data-fill=total_paid]').html(res.data.total_paid);
                    $('[data-fill=total_arrears]').html(res.data.total_arrears);
                    $('[data-fill=due_amount]').html(res.data.due_amount);
                } else {
                    toastr.error('Cannot load loan details.', 'Error');
                }
            }, 'json');
        }

        $(function () {
            init();
        });
    </script>
@endsection
