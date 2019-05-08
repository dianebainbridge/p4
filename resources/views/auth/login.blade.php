@extends('layouts.master')

@section('title')
    Login
@endsection

@section('head')
    <link href='/css/fuelCalculator/form.css' rel='stylesheet'>
@endsection

@section('content')
    <h2>Login</h2>
    <p>Don't have an account? <a href='/register'>Register here...</a></p>
    <hr/>
    <div class="col-sm-6">
        <form method='POST' action='{{ route('login') }}'>
            {{ csrf_field() }}

            <div class="form-group">
                <label for='email'>E-Mail Address</label>
                <input id='email' type='email' name='email' value='{{ old('email') }}' required autofocus>
                @include('includes.error-field', ['fieldName' => 'email'])
            </div>
            <div class="form-group">
                <label for='password'>Password</label>
                <input id='password' type='password' name='password' required>
                @include('includes.error-field', ['fieldName' => 'password'])
            </div>
            <div class="form-group">
                <label for='remember'>Remember Me</label>
                <input type='checkbox' name='remember' {{ old('remember') ? 'checked' : '' }}>
            </div>
            <div class="form-group">
                <button type='submit' class='btn btn-primary'>Login</button>
            </div>
        </form>
        <a class='btn btn-link' href='{{ route('password.request') }}'>Forgot Your Password?</a>
    </div>
@endsection
