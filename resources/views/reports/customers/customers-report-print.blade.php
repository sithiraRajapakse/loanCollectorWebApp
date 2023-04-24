@extends('layouts.report-print')

@section('title')
    Customers List
@endsection

@section('styles')
@endsection

@section('content')
    <h1>Customers List</h1>

    <table class="">
        <colgroup>
            <col style="text-align: left;">
            <col style="text-align: left;">
            <col style="text-align: left;">
            <col style="text-align: left;">
            <col style="text-align: left;">
            <col style="text-align: left;">
        </colgroup>
        <thead>
        <tr>
            <th style="text-align: left;">Name</th>
            <th style="text-align: left;">NIC No.</th>
            <th style="text-align: left;">Address</th>
            <th style="text-align: left;">Telephone</th>
            <th style="text-align: left;">Email</th>
            <th style="text-align: left;">Location</th>
        </tr>
        </thead>
        <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ ucwords($customer->name) }}</td>
            <td>{{ ucwords($customer->nic_no) }}</td>
            <td>{{ ucwords($customer->address) }}</td>
            <td>{{ ucwords($customer->telephone) }}</td>
            <td>{{ ucwords($customer->email) }}</td>
            <td>{{ ucwords($customer->location) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6">No customer data available.</td>
        </tr>
        </tbody>
    </table>
@endsection

@section('scripts')

@endsection
