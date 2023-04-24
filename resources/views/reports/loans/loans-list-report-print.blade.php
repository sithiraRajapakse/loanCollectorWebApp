@extends('layouts.report-print')

@section('title')
    Loans List Report
@endsection

@section('styles')
@endsection

@section('content')
    <h3>Loans List Report</h3>
    <table>
        <thead>
        <tr>
            <th>Loan Number</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Period</th>
            <th>Type</th>
            <th>Loan Amount</th>
            <th>Interest Rate</th>
            <th>Interest Total</th>
            <th>Installments</th>
            <th>Installment Amount</th>
            <th>Total Arrears</th>
            <th>Completed On</th>
        </tr>
        </thead>
{{--
        'date', 'loan_number', 'customer_id', 'scheme_id',
        'start_date', 'end_date', 'loan_amount', 'interest_rate', 'interest_total',
        'no_of_installments', 'installment_amount', 'last_installment_type',
        'completed_at', 'completed_by_id', 'created_by_id', 'is_extended', 'extended_date', 'total_arrears'
--}}
        <tbody>
            @foreach($loans as $loan)
                <tr>
                    <td>{{ $loan->loan_number }}</td>
                    <td>{{ $loan->date }}</td>
                    <td>{{ $loan->customer->name }}</td>
                    <td>{{ sprintf('%s to %s', $loan->start_date, $loan->end_date) }}</td>
                    <td>{{ $loan->scheme->title }}</td>
                    <td>{{ number_format($loan->loan_amount, 2) }}</td>
                    <td>{{ $loan->interest_rate }}</td>
                    <td>{{ number_format($loan->interest_total, 2) }}</td>
                    <td>{{ $loan->no_of_installments }}</td>
                    <td>{{ number_format($loan->installment_amount, 2) }}</td>
                    <td>{{ number_format($loan->total_arrears, 2) }}</td>
                    <td>{{ $loan->completed_at ? date('Y-m-d', strtotime($loan->completed_at)) : 'Ongoing' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')

@endsection
