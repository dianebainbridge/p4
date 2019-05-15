<?php

namespace App\Actions\FuelLogEntry;

class UpdateFuelLogEntry
{
    public function __construct($fillUpData, $fuelLogEntry, $user)
    {
        #update the entry with the passed id in  the fuel log entries table
        $this->fillup_date = $fillUpData['fillupDate'];
        $fuelLogEntry->fillup_date = $fillUpData['fillupDate'];
        $fuelLogEntry->start_distance = $fillUpData['startDistance'];
        $fuelLogEntry->end_distance = $fillUpData['endDistance'];
        $fuelLogEntry->distance_units = $fillUpData['distanceUnit'];
        $fuelLogEntry->fuel_volume = $fillUpData['fuelVolume'];
        $fuelLogEntry->fuel_units = $fillUpData['volumeUnit'];
        $fuelLogEntry->user_id = $user->id;
        $fuelLogEntry->save();
    }
}