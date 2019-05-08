@extends('layouts.master')

@section('title')
    Fuel Consumption Calculator
@endsection

@section('head')
    <link href='/css/fuelCalculator/form.css' rel='stylesheet'>
@endsection

@section('content')
    <h2 class="header">Edit Fuel Log Entry for fill-up date {{ $fuelLogEntry->fillup_date }}</h2>
    <hr/>
    <div class="row">
        <div class="col-sm-6">
            <form method='POST' action='/fuelConsumptionCalculator/update-fuel-log-entry/{{ $fuelLogEntry->id }}'>
                {{ csrf_field() }}
                {{ method_field('put') }}
                <div class="form-group">
                    <i class="fas fa-tachometer-alt"></i><!--fontawesome kind of odometer icon-->
                    <label for="startDistance">Odometer reading - last fill-up </label>
                    <input id="startDistance" name="startDistance" type="number" value="{{ old('startDistance',$fuelLogEntry->start_distance) }}"/>
                    {{-- Display the first error if there are any fof this input --}}
                    @include('includes.error-field', ['fieldName' => 'startDistance'])
                </div>
                <div class="form-group">
                    <i class="fas fa-tachometer-alt"></i>
                    <label for="endDistance">Odometer reading - this fill-up </label>
                    <input id="endDistance" name="endDistance" type="number" value="{{ old('endDistance',$fuelLogEntry->end_distance) }}"/>
                    {{-- Display the first error if there are any fof this input --}}
                    @include('includes.error-field', ['fieldName' => 'endDistance'])
                </div>
                <div class="form-group">
                    Select miles or kilometers
                    <br/>
                    <label class="radio-inline" for="miles">
                        <input type="radio" name="distanceUnit" value="miles" id="miles"
                                {{(old('distanceUnit',$fuelLogEntry->distance_units)=="miles") ? 'checked' : '' }}
                        >Miles
                    </label>
                    <label class="radio-inline" for="kilometers">
                        <input type="radio" id="kilometers" name="distanceUnit" value="kilometers"
                                {{(old('distanceUnit',$fuelLogEntry->distance_units)=="kilometers") ? 'checked' : '' }}
                        >Kilometers
                    </label>
                    @include('includes.error-field', ['fieldName' => 'distanceUnit'])
                </div>
                <div class="form-group">
                    <i class="fas fa-gas-pump"></i>
                    <label for="fuelVolume">Fuel Reading from Gas Pump </label>
                    <input id="fuelVolume" name="fuelVolume" type="text" value="{{old('fuelVolume',$fuelLogEntry->fuel_volume)}}"/>
                    @include('includes.error-field', ['fieldName' => 'fuelVolume'])
                </div>
                <div class="form-group">
                    <label for="volumeUnit">Select option</label>
                    <select id="volumeUnit" name="volumeUnit" class="custom-select volumeUnit">
                        <option value="">&#160;</option>
                        <option value="gallon" {{ (old('volumeUnit',$fuelLogEntry->fuel_units)=="gallon") ? 'selected' : '' }} >
                            Gallons
                        </option>
                        <option value="liter" {{ (old('volumeUnit',$fuelLogEntry->fuel_units)=="liter") ? 'selected' : '' }} >
                        >
                            Liters
                        </option>
                    </select>
                    @include('includes.error-field', ['fieldName' => 'volumeUnit'])
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{-- Display the fuel calculation if there are no errors or the erorrs if there are errors--}}
                <div class="outcome">
                    @if(count($errors) > 0)
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection