@extends('layouts.master')

@section('title') Loan Collectors Management @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Update Loan Collector  @endslot
        @slot('li_1') Change the loan collector account details and associated user account @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('collectors') }}" class="btn btn-info"> <span class="fa fa-arrow-left"></span> Back to all collectors</a>
                </div>
                <div class="card-body">
                    <h4 class="card-title">Update Basic Profile</h4>
                    <form action="{{ route('collectors.update', $collector->id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="collector_name">Name</label>
                                    <input class="form-control @error('collector_name') is-invalid @enderror"
                                           id="collector_name" name="collector_name" required=""
                                           autofocus value="{{ old('collector_name', $collector->name) }}">
                                    @error('collector_name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="nic_no">N.I.C. No.</label>
                                    <input class="form-control @error('nic_no') is-invalid @enderror"
                                           id="nic_no" name="nic_no" required=""
                                           value="{{ old('nic_no', $collector->nic_no) }}">
                                    @error('nic_no')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="drivers_license_no">Driver's License No.</label>
                                    <input class="form-control @error('drivers_license_no') is-invalid @enderror"
                                           id="drivers_license_no" name="drivers_license_no" required=""
                                           value="{{ old('drivers_license_no', $collector->drivers_license_no) }}">
                                    @error('drivers_license_no')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                           id="address" name="address" required=""
                                           value="{{ old('address', $collector->address) }}"/>
                                    @error('address')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="telephone_no">Telephone No.</label>
                                    <input class="form-control @error('telephone_no') is-invalid @enderror"
                                           id="telephone_no" name="telephone_no" required=""
                                           value="{{ old('telephone_no', $collector->telephone) }}">
                                    @error('telephone_no')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-group">
                                <button type="submit" id="register_collector" class="btn btn-primary">
                                    Update Collector Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($collector->user->user_type === \App\Enums\UserType::COLLECTOR)
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Collector User Account</h4>
                        <p class="card-title-desc text-info"><strong>Note: </strong>Please change the user account
                            details only if you need to. Email address is not allowed to change.</p>
                        <form action="{{ route('collectors.update-user', $collector->id) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="email_address">Email Address</label>
                                        <span class="form-control-plaintext">{{ $collector->user->email }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="password">New Password (If updating)</label>
                                        <input type="password" name="password" id="password" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="confirm-password">Confirm New Password (If updating)</label>
                                        <input type="password" name="password_confirmation" id="confirm-password"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('script')
    {{-- parsleyjs validation library --}}
    <script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
@endsection
