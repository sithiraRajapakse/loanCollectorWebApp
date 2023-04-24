@extends('layouts.master')

@section('title') User Management @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Users @endslot
        @slot('li_1') Manage system users here @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-success border-success text-white">
                    Create New User Account
                </div>
                <div class="card-body">
                    <form action="{{ route('users.create') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" name="name"
                                   id="name" required
                                   placeholder="Enter the user's name"/>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control @error('password') is-invalid @enderror"
                                   name="email" id="email" required
                                   placeholder="Enter user's email address"/>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-secondary" type="button" id="generate_password"
                                    name="generate_password" title="Generate Password">
                                <span class="fa fa-key"></span> Generate a password
                            </button>
                            <button class="btn btn-sm btn-info" type="button" id="show_hide_passwords"
                                    name="show_hide_passwords"
                                    title="Click to toggle visibility of the password fields">
                                <span class="fa fa-eye"></span> Show/Hide Password
                            </button>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" id="password" placeholder="Enter user's password" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="form-control @error('password') is-invalid @enderror" name="password"
                                   required placeholder="Enter password">
                        </div>
                        <div class="form-group">
                            <label for="user_type">User Type</label>
                            <select name="user_type" id="user_type" class="form-control">
                                <option value="{{ \App\Enums\UserType::SYSTEM_USER }}">System User</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="create_user" id="create_user" class="form-control">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ str_replace("_", ' ', $user->user_type) }}</td>
                        <td></td>
                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('styles-top')
@endsection

@section('script-bottom')
    <script>
        function generatePassword() {
            const characters = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "[", "]", "^", "_", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
            let passwordLength = 12;

            let password = '';

            for (let i = 0; i < passwordLength; i++) {
                let index = Math.floor(Math.random() * Math.floor(characters.length));
                password += characters[index];
            }
            return password;
        }

        $('#generate_password').click(function () {
            let pass = generatePassword();
            $('#password, #password_confirmation').val(pass);
            // copy(pass); // more effort needed to make this working
        });

        $('#show_hide_passwords').click(function () {
            if ($('#password, #password_confirmation').prop('type') == 'password') {
                $('#password, #password_confirmation').prop('type', 'text');
            } else {
                $('#password, #password_confirmation').prop('type', 'password');
            }
        });

        $(function () {});
    </script>
@endsection
