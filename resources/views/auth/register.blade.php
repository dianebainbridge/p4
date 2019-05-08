@extends('layouts.master')

@section('title')
    Register
@endsection

@section('head')
    <link href='/css/fuelCalculator/form.css' rel='stylesheet'>
@endsection

@section('content')

    <h2>Register</h2>
    <p>Already have an account? <a href='/login'>Login here...</a></p>
    <div class="col-sm-3">
        <form method='POST' action='{{ route('register') }}'>
            {{ csrf_field() }}
            <div class="form-group">
                <label for='name'>Name</label>
                <input id='name' type='text' name='name' value='{{ old('name') }}' required autofocus>
                @include('includes.error-field', ['fieldName' => 'name'])
            </div>
            <div class="form-group">
                <label for='email'>E-Mail Address</label>
                <input id='email' type='email' name='email' value='{{ old('email') }}' required>
                @include('includes.error-field', ['fieldName' => 'email'])
            </div>
            <div class="form-group">
                <label for='password'>Password (min: 8)</label>
                <input id='password' type='password' name='password' required>
                @include('includes.error-field', ['fieldName' => 'password'])
            </div>
            <div class="form-group">
                <label for='password-confirm'>Confirm Password</label>
                <input id='password-confirm' type='password' name='password_confirmation' required>
            </div>
            <div class="form-group">
                <button type='submit' class='btn btn-primary'>Register</button>
            </div>
        </form>
    </div>

@endsection
