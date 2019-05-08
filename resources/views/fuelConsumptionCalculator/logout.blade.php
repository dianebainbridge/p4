@extends('layouts.master')

@section('title')
    Logout
@endsection

@section('head')

@endsection

@section('content')
    <h2>Logout</h2>
    <hr/>
    <p>Are you sure you want to logout?</p>
    <div class="col-sm-1">
        <form method='POST' id='logout' action='/logout'>
            {{ csrf_field() }}
            <ul style="list-style:none">
                <li><i class="fas fa-sign-out-alt"></i> <a href='#' onClick='document.getElementById("logout").submit();'>Yes</a></li>
                <li><i class="fas fa-undo"></i> <a href='/'>No</a></li>
            </ul>
        </form>
    </div>
@endsection

