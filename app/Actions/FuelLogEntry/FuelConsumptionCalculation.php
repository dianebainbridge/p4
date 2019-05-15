<?php

namespace App\Actions\FuelLogEntry;

class FuelConsumptionCalculation
{
    public function __construct($fuelLog)
    {
        $this->fuel_consumed = 0;
        #calculate the amount of fuel consumed and return the value
        $distance = (float)number_format($fuelLog['endDistance'] - $fuelLog['startDistance'], 1, '.', '');
        $this->fuel_consumed = number_format($distance / $fuelLog['fuelVolume'], 1, '.', '');
        return $this->fuel_consumed;
    }
}