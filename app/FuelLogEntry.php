<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FuelLogEntry extends Model
{
    public function user()
    {
        #Fuel log entries belong to user
        return $this->belongsTo('App\User');
    }

    public function getFuelConsumed()
    {
        $distance = (float)number_format($this->end_distance - $this->start_distance, 1, '.', '');
        return (float)number_format($distance / $this->fuel_volume, 1, '.', '');
    }

    public function getDistance()
    {
        return $this->distance . " " . $this->distance_units;
    }

    public function getFuel()
    {
        return $this->fuel_volume . " " . $this->fuel_units . "s";
    }


}
