@extends('layouts.master-without-nav')

@section('title')
    Login
@endsection

@section('body')
    <body>
    @endsection

    @section('content')
        <div class="account-pages my-5 pt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-soft-primary">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p>Please Sign In.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div>
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('assets/images/logo_sm.png') }}" alt="" class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input name="email" type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   @if(old('email')) value="{{ old('email') }}" @endif id="username"
                                                   placeholder="Enter username" autocomplete="email" autofocus>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="userpassword">Password</label>
                                            <input type="password" name="password"
                                                   class="form-control  @error('password') is-invalid @enderror"
                                                   id="userpassword" placeholder="Enter password">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                   id="customControlInline">
                                            <label class="custom-control-label" for="customControlInline">Remember
                                                me</label>
                                        </div>

                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-block waves-effect waves-light"
                                                    type="submit">Log In
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>

                        <div class="mt-5 text-center">
                            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                            <p>Crafted with <i class="mdi mdi-heart text-danger"></i>
                                by {{ config('common.developer.name') }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection
