@extends('layouts.master')

@section('title') Daily Loan @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Register New Daily Loan @endslot
        @slot('li_1') @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-5">
            <div class="card border border-success">
                <div class="card-body">
                    <p>Fields marked with <span style="color: #ff0000;">*</span> are required.</p>

                    <form action="{{ route('customer-loans.daily.create') }}" method="post">
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
                                    <label for="loan_amount" required>Loan Amount</label>
                                    <div class="controls">
                                        <input type="number" name="loan_amount" id="loan_amount"
                                               class="form-control" min="1" data-trigger="loan-calc"
                                               step="1" placeholder="0.00" value="{{ old('loan_amount') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="interest_rate" required>Interest Rate</label>
                                    <div class="controls">
                                        <input type="number" name="interest_rate" id="interest_rate"
                                               class="form-control" data-trigger="loan-calc"
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
                                               class="form-control"
                                               value="{{ old('start_date') }}" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="installments_count" required>No. of days</label>
                                    <div class="controls">
                                        <input type="number" name="installments_count" id="installments_count"
                                               class="form-control" min="1" max="999999" required
                                               data-trigger="loan-calc"
                                               step="1" value="{{ old('installments_count') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="end_date" required>End Date</label>
                                    <div class="controls">
                                        <input type="text" name="end_date" id="end_date"
                                               class="form-control"
                                               value="{{ old('end_date') }}" required readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="installment_amount" required>Installment Amount</label>
                                    <div class="controls">
                                        <input type="number" name="installment_amount" id="installment_amount"
                                               class="form-control" min="1" step="0.01" data-trigger="loan-calc"
                                               value="{{ old('installment_amount') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="send_reg" name="send_reg" class="btn btn-success">
                                Register Loan
                            </button>
                            <button type="button" class="btn btn-primary float-right" id="trigger-query-installments">
                                Preview Installments <span class="fa fa-arrow-circle-right"></span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            {{-- show selected customer's information first --}}
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
            <div class="card border border-success">
                <div class="card-header bg-success text-white">
                    Installments Preview
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="installmentsTable">
                        <thead>
                        <tr>
                            <th>Installment No.</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4">Installments will appear here when the details are filled.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="loan_confirmation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Please Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>You are about to create a <strong>Daily Loan</strong>. Please check the following details and
                        click <strong>Create</strong> button if the details are correct and you are ready to proceed.
                    </p>
                    <table class="table table-bordered table-striped">
                        <colgroup>
                            <col style="width: 50%;">
                            <col style="width: 50%;">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td>Customer</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Expected Loan Amount</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Interest Rate</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>No. of Installments</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Installment Amount</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Start Date</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>End Date</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="proceed_create_daily">Create</button>
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
        window['moment-range'].extendMoment(moment);
        const holidays = {!! $holidays->pluck('date')->toJson() !!};

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

        function _queryInstallmentsPreview() {
            let loan_amount = $('#loan_amount').val();
            let interest_rate = $('#interest_rate').val();
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();
            let installments_count = $('#installments_count').val();
            let installment_amount = $('#installment_amount').val();
            let data = {
                loan_amount: loan_amount,
                interest_rate: interest_rate,
                start_date: start_date,
                end_date: end_date,
                installments_count: installments_count,
                installment_amount: installment_amount,
                type: '{{\App\Enums\SchemeType::DAILY}}'
            };
            let container = $('#installmentsTable tbody');
            $.get('{{ route('customer-loans.installments.daily') }}', data, function (res) {
                let installments = res.data.installments;
                if (installments.length == 0) {
                    let temp_empty = `<tr><td colspan="4">Installments will appear here when the details are filled.</td></tr>`;
                    container.html(temp_empty);
                    $('#send_reg').prop('disabled', true);
                } else {
                    let ht = '';
                    let num = 1;
                    $.each(installments, function (i, x) {
                        let temp_row = `<tr>
                                    <td>${num}</td>
                                    <td>${x.date}</td>
                                    <td>${x.amount}</td>
                                </tr>`;
                        ht += temp_row;
                        num++;
                    });
                    container.html(ht);
                    $('#send_reg').prop('disabled', false);
                }
            }, 'json');
        }

        $('#customer_id').change(function () {
            queryCustomerInformation();
        });

        $('#installments_count').change(function () {
            let installmentCount = $(this).val();
            let sd = $('#start_date').val();
            let ed = moment(sd).add(installmentCount, 'days');
            $('#end_date').val(ed.format('YYYY-MM-DD'));
        });

        function calculateDays(callback) {
            let startDate = moment($('#start_date').val());
            let endDate = moment($('#end_date').val());
            let days = endDate.diff(startDate, 'days');
            $('#installments_count').val(days);
            if (typeof callback === 'function')
                callback();
        }

        function getHolidaysBetweenDates(sd, ed) {
            let range = moment.range(sd, ed);
            // console.log('range start: ' + range.start);
            // console.log('range end: ' + range.end);
            let hols = [];
            holidays.forEach(function (i, x) {
                // console.log(`Checking if ${i} is between ${range.start} and ${range.end}`);
                if (moment(i).within(range)) {
                    hols.push(i);
                    // console.log(i + ' within range. adding to hols.');
                } else {
                    // console.log(i + ' not in range. not added.');
                }
            });
            // console.debug(hols.join(" ** "));
            return hols;
        }

        function calculate() {
            let loanAmount = Number($('#loan_amount').val());
            let interestRate = Number($('#interest_rate').val());

            // calculate start and end date difference as installment count
            let _st = $('#start_date').val();
            let startDate = moment(_st);
            let endDate = moment(_st);
            // console.log(`start date is ${startDate.format('YYYY-MM-DD')}`)

            let noOfDays = $('#installments_count').val();
            // console.log(`Number of days ${noOfDays}`);
            let tempEndDate = moment(_st);
            tempEndDate.add(noOfDays, 'days');
            // console.log(`temporary end date is ${tempEndDate.format('YYYY-MM-DD')}`);

            // calculate months
            let noOfMonths = Math.round(tempEndDate.diff(startDate, 'months', true));
            noOfMonths = Math.ceil(noOfDays / 30); // no of months
            // console.log(`No of months is ${noOfMonths}`);
            // calculate total interest
            let totalInterest = (loanAmount / 100) * (interestRate * noOfMonths);
            // console.log(`Interest rate ${interestRate}`);
            // console.log(`Total interest ${totalInterest}`);
            let totalLoanAmount = loanAmount + totalInterest;
            // console.log(`Total loan amount ${totalLoanAmount}`);

            // calculate number of installments by loan amount and installment amount
            let installmentAmount = $('#installment_amount').val();
            // console.log(`Installment amount is ${installmentAmount}`);
            let installmentsCount = noOfDays;
            let remainder = totalLoanAmount % installmentAmount;
            // console.log(`remainder is ${remainder}`);
            if (remainder > 0) {
                // different first installment
                installmentsCount = (totalLoanAmount - remainder) / installmentAmount;
                // console.log(`has remainder - installments count minus remainder is ${installmentsCount}`);
            } else {
                // similar installments
                installmentsCount = totalLoanAmount / installmentAmount;
                // console.log(`no remainder - installment count is ${installmentsCount}`);
            }

            // calculate the real installments count
            // console.log(`adding installments count (${installmentsCount}) to end date`);
            endDate.add(installmentsCount - 1, 'days');
            // console.log(`new end date is ${endDate}`);

            installmentsCount = remainder > 0 ? installmentsCount + 1 : installmentsCount;
            $('#installments_count').val(installmentsCount);

            let holidaysBetween = getHolidaysBetweenDates(startDate, endDate);
            // console.log('holidays between: ' + holidaysBetween.length);
            // console.log(holidaysBetween.join('; '));
            endDate.add(holidaysBetween.length, 'days'); //  add days equal to the number of holidays inbetween the start and end dates as calculated

            // show the installments
            let installments = [];
            let installmentsRange = moment.range(startDate, endDate);
            // console.log(installmentsRange.start.format('YYYY-MM-DD'));
            // console.log(installmentsRange.end.format('YYYY-MM-DD'));

            let installmentList = getInstallmentDatesWithoutHolidays(startDate, installmentsCount);
            // keys: start_date, end_date, days, installments, holidays
            // set data in the fields
            $('#end_date').val(installmentList.end_date);

            let isFirst = true;
            let insts = installmentList.installments;
            insts.forEach(function (inst) {
                if (inst.type === "day") {
                    let _installmentAmount = isFirst ? (remainder > 0 ? remainder : installmentAmount) : installmentAmount;
                    installments.push({
                        date: inst.date,
                        amount: _installmentAmount,
                    });
                    isFirst = false;
                } else {
                    installments.push({
                        date: inst.date,
                        amount: 0,
                    });
                }
            });

            // fill the installments table
            let container = $('#installmentsTable tbody');
            if (installments.length > 0) {
                let row_num = 1;
                let ht = '';
                installments.forEach(function (i, x) {
                    if (!_isHoliday(i.date)) {
                        let temp_row = `<tr>
                            <td>${row_num}</td>
                            <td>${i.date}</td>
                            <td>${i.amount}</td>
                        </tr>`;
                        ht += temp_row;
                        row_num++;
                    } else {
                        ht += `<tr class="table-danger"><td colspan="3" class="text-center">${i.date} is a holiday.</td></tr>`;
                    }
                });
                container.html(ht);
            } else {
                let no_rows = '<tr><td colspan="4">Installments will appear here when the details are filled.</td></tr>';
                container.html(no_rows);
            }
        }

        function getInstallmentDatesWithoutHolidays($startDate, $installmentsCount) {
            // get the installments tentative start and end dates
            let start_date = moment($startDate);
            let end_date = moment($startDate)

            // calculate the days count
            let day = 0;
            let holidays = [];
            let installments = [];
            // count days excluding holidays
            while (day < $installmentsCount) {
                if (day >= $installmentsCount) {
                    break;
                }
                let td = moment(end_date.format('YYYY-MM-DD'))
                if (!_isHoliday(td.format('YYYY-MM-DD'))) {
                    installments.push({
                        installment: day + 1,
                        date: td.format('YYYY-MM-DD'),
                        type: 'day',
                        amount: 0
                    });
                    day++;
                } else {
                    holidays.push(td.format('YYYY-MM-DD'));
                    installments.push({
                        date: td.format('YYYY-MM-DD'),
                        type: 'holiday',
                    });
                }
                if (day < $installmentsCount) {
                    end_date.add(1, 'day');
                }
            }
            const data = {
                start_date: start_date.format('YYYY-MM-DD'),
                end_date: end_date.format('YYYY-MM-DD'),
                days: day,
                installments: installments,
                holidays: holidays
            };
            console.log(data);
            return data;
        }

        function _isHoliday(date) {
            return holidays.includes(date);
        }

        $(function () {
            $('.select2ize').select2();
            queryCustomerInformation();

            $('#start_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                datesDisabled: {!! $holidays->pluck('date')->toJson() !!}
            }).on('changeDate', function (e) {
                calculateDays();
            });

            $('#end_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                datesDisabled: {!! $holidays->pluck('date')->toJson() !!}
            }).on('changeDate', function (e) {
                calculateDays();
            });

            $('#trigger-query-installments').click(function () {
                calculate();
            });
        });
    </script>
@endsection
