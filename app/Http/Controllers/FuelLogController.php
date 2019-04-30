<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FuelLog;

class FuelLogController extends Controller
{
    public function test_model()
    {
        $fuel_log = new FuelLog();
        $fuel_log->distance = 10;
        $fuel_log->distance_units = 'miles';
        $fuel_log->fuel_volume = 100;
        $fuel_log->fuel_units = 'gallon';



        # Invoke the Eloquent `save` method to generate a new row in the
        # `fuel logs` table, with the above data
        $fuel_log->save();

        dump($fuel_log->distance);
    }
}
