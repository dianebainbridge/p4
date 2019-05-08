<?php

use Illuminate\Database\Seeder;
use App\FuelLogEntry;
use App\User;
use Carbon\Carbon;

class FuelLogEntriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        # Array of fuel log entries to add
        $fuel_log_entries = [
            [Carbon::now(),10,'gallon',23000,27000,'miles','diane.bainbridge@gmail.com'],
            [Carbon::now(),12,'gallon',35000,370520,'miles','diane.bainbridge@gmail.com'],
        ];
        $count =count($fuel_log_entries);

        #Loop through entries and add to database
        foreach($fuel_log_entries as $key => $fuelLogEntryData)
        {
            #get the user id of the user with the username entered
            $user_id = User::where('email', '=', $fuelLogEntryData[6])->pluck('id')->first();
            $fuelLogEntry = new FuelLogEntry();
            $fuelLogEntry->created_at = Carbon::now()->subDays($count)->toDateTimeString();
            $fuelLogEntry->updated_at = Carbon::now()->subDays($count)->toDateTimeString();
            $fuelLogEntry->fillup_date = $fuelLogEntryData[0];
            $fuelLogEntry->fuel_volume = $fuelLogEntryData[1];
            $fuelLogEntry->fuel_units = $fuelLogEntryData[2];
            $fuelLogEntry->start_distance = $fuelLogEntryData[3];
            $fuelLogEntry->end_distance = $fuelLogEntryData[4];
            $fuelLogEntry->distance_units = $fuelLogEntryData[5];
            $fuelLogEntry->user_id = $user_id;

            $fuelLogEntry->save();
            $count --;
        }
    }
}
