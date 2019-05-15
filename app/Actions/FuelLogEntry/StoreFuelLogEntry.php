<?php

namespace App\Actions\FuelLogEntry;

use App\FuelLogEntry;
use Carbon\Carbon;

class StoreFuelLogEntry
{
    #insert  a record for this the fillup data into the fuel_log_entries database for this user
    public function __construct($fillUpData,$user)
    {
        #save the entry to the fuel log entries table
        $fuelLog = new FuelLogEntry();
        $fuelLog->fillup_date = $fillUpData['fillupDate'];
        $fuelLog->start_distance = $fillUpData['startDistance'];
        $fuelLog->end_distance = $fillUpData['endDistance'];
        $fuelLog->distance_units = $fillUpData['distanceUnit'];
        $fuelLog->fuel_volume = $fillUpData['fuelVolume'];
        $fuelLog->fuel_units = $fillUpData['volumeUnit'];
        $fuelLog->user_id = $user->id;

        # Invoke the Eloquent `save` method to generate a new row in the
        # `fuel logs` table, with the above data
        $fuelLog->save();
    }
}