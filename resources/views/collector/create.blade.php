@extends('layouts.master')

@section('title') Loan Collectors Management @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Loan Collectors @endslot
        @slot('li_1') Register New Loan Collector Here @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('collectors.create') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <h6>Basic Profile</h6>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="collector_name">Name</label>
                                    <input class="form-control @error('collector_name') is-invalid @enderror"
                                           id="collector_name" name="collector_name" required=""
                                           autofocus value="{{ old('collector_name') }}">
                                    @error('collector_name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="nic_no">N.I.C. No.</label>
                                    <input class="form-control @error('nic_no') is-invalid @enderror"
                                           id="nic_no" name="nic_no"
                                           value="{{ old('nic_no') }}"
                                           pattern="^([1-9]{1}[0-9]{8}[vVxX])|([1-2]{1}[0-9]{11})$">
                                    @error('nic_no')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="drivers_license_no">Driver's License No.</label>
                                    <input class="form-control @error('drivers_license_no') is-invalid @enderror"
                                           id="drivers_license_no" name="drivers_license_no"
                                           value="{{ old('drivers_license_no') }}">
                                    @error('drivers_license_no')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                           id="address" name="address" value="{{ old('address') }}"/>
                                    @error('address')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="telephone_no">Telephone No.</label>
                                    <input class="form-control @error('telephone_no') is-invalid @enderror"
                                           id="telephone_no" name="telephone_no"
                                           value="{{ old('telephone_no') }}" pattern="^0[1-9]{1}[0-9]{8}$">
                                    @error('telephone_no')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <h6>User Account</h6>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="email_address">Email Address</label>
                                    <input type="email" name="email_address" id="email_address" class="form-control"
                                           required>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="confirm-password">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="confirm-password"
                                           class="form-control" required>
                                </div>
                            </div>

                        </div>
                        <div class="mt-3">
                            <div class="form-group">
                                <button type="submit" id="register_collector" class="btn btn-primary">
                                    Register Collector
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>N.I.C. No.</th>
                    <th>Drivers License No.</th>
                    <th>Address</th>
                    <th>Telephone No.</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($collectors as $collector)
                    @if($collector->user->user_type === \App\Enums\UserType::COLLECTOR)
                        <tr>
                            <td>{{ $collector->name }}</td>
                            <td>{{ $collector->nic_no }}</td>
                            <td>{{ $collector->drivers_license_no }}</td>
                            <td>{{ $collector->address }}</td>
                            <td>{{ $collector->telephone }}</td>
                            <td>
                                <a href="{{ route('collectors.edit', $collector->id) }}"
                                   class="btn btn-sm btn-info edit_collector">Edit</a>
                                <form action="{{ route('collectors.delete', $collector->id) }}" method="post"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to remove this collector profile?\nRemoving this will remove both collector and user profile associated with it.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger delete_collector">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6">No collectors registered in the system.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
