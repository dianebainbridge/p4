<@extends('layouts.master')

    @section('title')
    Login
            @endsection

            @section('title')
            Confirm deletion: {{ $fuelLogEntry->fillup_date }}
@endsection

@section('content')
    <h2>Confirm deletion</h2>

    <p>Are you sure you want to delete the entry for fill-up date {{ $fuelLogEntry->fillup_date }}?</p>

    <form method='POST' action='/fuelConsumptionCalculator/delete-fuel-log-entry/{{ $fuelLogEntry->id }}'>
        {{ method_field('delete') }}
        {{ csrf_field() }}
        <input type='submit' value='Delete entry' class='btn btn-danger btn-small'>
    </form>

    <p>
        <a href='/fuelConsumptionCalculator/get-fuel-log'>Don't delete this entry.</a>
    </p>

@endsection