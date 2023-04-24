@extends('layouts.master')

@section('title') New Customer Registration @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Customer Registration @endslot
        @slot('li_1') Register New Customers @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('customers.process-registration') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="customer_name">Name</label>
                            <input class="form-control @error('customer_name') is-invalid @enderror" id="customer_name"
                                   name="customer_name" required="" placeholder="Enter full name of the customer here"
                                   autofocus value="{{ old('customer_name') }}">
                            @error('customer_name')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="nic_no">N.I.C. No.</label>
                                    <input class="form-control @error('nic_no') is-invalid @enderror" id="nic_no"
                                           name="nic_no" placeholder="Enter N.I.C. number here"
                                           value="{{ old('nic_no') }}" pattern="^([1-9]{1}[0-9]{8}[vVxX])|([1-2]{1}[0-9]{11})$">
                                    @error('nic_no')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="telephone">Telephone</label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                       id="telephone" name="telephone" placeholder="Enter telephone number here"
                                       value="{{ old('telephone') }}" pattern="^0[1-9]{1}[0-9]{8}$">
                                @error('telephone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-lg-5">
                                <label for="email_address">Email Address</label>
                                <input type="email" class="form-control @error('email_address') is-invalid @enderror"
                                       name="email_address" id="email_address"
                                       placeholder="Enter email Address of customer here"
                                       value="{{ old('email_address') }}">
                                @error('email_address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-lg-7">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" cols="30" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Enter customer address here">{{ old('address') }}</textarea>
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-lg-5">
                                <label for="location">Location / Whereabouts</label>
                                <textarea name="location" id="location" cols="30" rows="3"
                                          class="form-control @error('location') is-invalid @enderror"
                                          placeholder="Enter customer's location details such as landmarks, whereabouts details here..">{{ old('location') }}</textarea>
                                @error('location')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-group">
                                <button type="submit" id="register_customer" class="btn btn-primary">Register Customer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
