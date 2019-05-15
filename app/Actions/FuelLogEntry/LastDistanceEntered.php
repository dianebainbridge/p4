<?php

namespace App\Actions\FuelLogEntry;

use App\FuelLogEntry;

class LastDistanceEntered
{
    private $userId;
    private $endDistance;

    public function __construct($user)
    {

        $this->lastEndDistance = '';
        $this->userId = $user->id;
    }
    #get the last end distance entered and return the value
    public function getLastEndDistance()
    {
        if (!is_null($this->userId)) {
            $this->lastEndDistance = FuelLogEntry::orderBy('created_at', 'desc')->where('user_id', '=', $this->userId)->limit(1)->pluck('end_distance');
            return $this->lastEndDistance;
        }
    }
}