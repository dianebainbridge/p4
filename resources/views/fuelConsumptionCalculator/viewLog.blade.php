@extends('layouts.master')

@section('title')
    View Fuel Log
@endsection

@section('content')
    <h2>View Fuel Log</h2>
    @if(count($fuelLog)>0)
        <p><a class="btn btn-info" href="/export">Export Fuel Log to Excel</a></p>
        <table id="table" class="table table-striped table-bordered table-hover table-condensed" style=width:75%>
            <thead>
            <tr>
                <th data-field="date">Date</th>
                <th data-field="distance">Distance</th>
                <th data-field="fuel">Fuel</th>
                <th data-field="fuelConsumption">Fuel Consumption</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($fuelLog as $logEntry)
                <tr>
                    <td>{{ $logEntry['fillup_date']}}</td>
                    <td>{{ $logEntry->getDistance() }}</td>
                    <td>{{ $logEntry->getFuel() }}</td>
                    <td>{{ $logEntry->getFuelConsumed() }} {{$logEntry['distance_units'] }}
                        /{{ $logEntry['fuel_units' ]}} </td>
                    <td>
                        <ul style="list-style:none">
                            <li><a href="/fuelConsumptionCalculator/edit-fuel-log-entry/{{ $logEntry->id }}"><i
                                            class="fas fa-edit"></i> Edit</a></li>
                            <li><a href="/fuelConsumptionCalculator/delete/{{ $logEntry->id }}'"><i
                                            class="far fa-trash-alt"></i> Delete</a></li>
                        </ul>
                    </td>
                </tr>
            @endforeach
            @if(!empty($average))
                <tr>
                    <td></td>
                    <td>{{$totalDistance}} {{$logEntry['distance_units'] }}</td>
                    <td>{{$totalFuel}} {{ $logEntry['fuel_units' ]}}</td>
                    <td>{{$average}}  {{$logEntry['distance_units'] }}/{{ $logEntry['fuel_units' ]}}</td>
                    <td></td>
                </tr>
            @endif
            </tbody>
        </table>
    @endif
    <p><a href="/"><i class="fas fa-plus-square"></i> Add a new fuel log entry</p></a>

@endsection
