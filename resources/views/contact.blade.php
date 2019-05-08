@extends('layouts.master')

@section('title')
    Contact
@endsection

@section('content')
    <h2>Contact</h2>
    <hr/>
    <i class="far fa-envelope"></i>&#160;<a href="mailto:{{ config('mail.supportEmail') }}">{{ config('app.name')}}
        Support</a>
@endsection