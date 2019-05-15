@extends('layouts.master')

@section('title')
    Fuel Consumption Calculator
@endsection

@section('head')
    <link href='/css/fuelCalculator/form.css' rel='stylesheet'>
@endsection

@section('content')
    <h2 class="header">Fuel Consumption Calculator</h2>
    <hr/>
    <div class="row">
        <div class="col-sm-6">
            <p class="subHeader">Use the steps below to calculate your fuel consumption.</p>
            <ol>
                <li>Enter the odometer reading from the last time you filled your gas tank.</li>
                <li>Enter the odometer reading at the time of your current fill up.</li>
                <li>Enter the amount of fuel required to completely fill your tank at this fill up.</li>
                <li>Be sure to select the units you are using to measure your distance and the amount of gas.</li>
            </ol>
            <p class="note">
                Note: before you use this the first time completely fill up your gas tank
                and save the distance read from your odometer.
                <br/>
                To save your entries to the fuel log login above.
            </p>
        </div>
        <div class="col-sm-6">
            <form method='POST' action='/fuelConsumptionCalculator/form-process'>
                @csrf
                <div class="form-group">
                    <i class="fas fa-calendar-day"></i><!--fontawesome kind of odometer icon-->
                    <label for="fillupDate">Date of fill-up </label>
                    <input id="fillupDate" name="fillupDate" type="date"
                           value="{{ old('fillupDate',$fillupDate) }}"/>
                    {{-- Display the first error if there are any fof this input --}}
                    @include('includes.error-field', ['fieldName' => 'fillupDate'])
                </div>
                <div class="form-group">
                    <i class="fas fa-tachometer-alt"></i><!--fontawesome kind of odometer icon-->
                    <label for="startDistance">Odometer reading - last fill-up </label>
                    <input id="startDistance" name="startDistance" type="number"
                           value="{{ old('startDistance',$startDistance) }}"/>
                    {{-- Display the first error if there are any fof this input --}}
                    @include('includes.error-field', ['fieldName' => 'startDistance'])
                </div>
                <div class="form-group">
                    <i class="fas fa-tachometer-alt"></i>
                    <label for="endDistance">Odometer reading - this fill-up </label>
                    <input id="endDistance" name="endDistance" type="number"
                           value="{{ old('endDistance',$endDistance) }}"/>
                    {{-- Display the first error if there are any fof this input --}}
                    @include('includes.error-field', ['fieldName' => 'endDistance'])
                </div>
                <div class="form-group">
                    Select miles or kilometers
                    <br/>
                    <label class="radio-inline" for="miles">
                        <input type="radio" name="distanceUnit" value="miles" id="miles"
                                {{-- If the old value i "miles" check this radio input--}}
                                {{(old('distanceUnit',$distanceUnit)=="miles") ? 'checked' : '' }}
                        >Miles
                    </label>
                    <label class="radio-inline" for="kilometers">
                        <input type="radio" id="kilometers" name="distanceUnit" value="kilometers"
                                {{-- If the old value i "kilometers" check this radio input--}}
                                {{(old('distanceUnit',$distanceUnit)=="kilometers") ? 'checked' : '' }}
                        >Kilometers
                    </label>
                    {{-- Display the first error if there are any fof this input --}}
                    @include('includes.error-field', ['fieldName' => 'distanceUnit'])
                </div>
                <div class="form-group">
                    <i class="fas fa-gas-pump"></i>
                    <label for="fuelVolume">Fuel Reading from Gas Pump </label>
                    <input id="fuelVolume" name="fuelVolume" type="text" value="{{old('fuelVolume',$fuelVolume) }}"/>
                    @include('includes.error-field', ['fieldName' => 'fuelVolume'])
                </div>
                <div class="form-group">
                    <label for="volumeUnit">Select option</label>
                    <select id="volumeUnit" name="volumeUnit" class="custom-select volumeUnit">
                        <option value="">&#160;</option>
                        <option value="gallon" {{ (old('volumeUnit',$volumeUnit)=="gallon") ? 'selected' : '' }} >
                            Gallons
                        </option>
                        <option value="liter" {{old('volumeUnit',$volumeUnit)=="liter" ? 'selected': ''}}>
                            Liters
                        </option>
                    </select>
                    {{-- Display the first error if there are any fof this input --}}
                    @include('includes.error-field', ['fieldName' => 'volumeUnit'])
                </div>
                {{-- Only logged in users can save to database --}}
                @if($logged_in_user != null)
                    <div class="form-group">
                        <label for="addLog" class="checkbox-inline">
                            <input type="checkbox" id="addLog" name="addLog"
                                   {{-- If the old value is "on" select this option--}}
                                   @if (old('addLog')=="on")
                                   checked="checked"
                                    @endif
                            ><b>Add to log</b>
                        </label>
                    </div>
                @endif
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </div>
                {{-- Display the fuel calculation if there are no errors or the erorrs if there are errors--}}
                <div class="outcome">
                    @if(count($errors)==0)
                        @if(!empty($fuelConsumed))
                            <p class="calculationResult">
                                Your traveled {{$distance}} {{$distanceUnit}} on {{$fuelVolume}} {{$volumeUnit}}s
                                of gas since your last fillup.
                                <br/>Fuel Consumed : {{$fuelConsumed}}  {{$distanceUnit}}/{{$volumeUnit}}
                            </p>
                        @endif
                    @else
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