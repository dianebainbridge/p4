<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App;
use App\FuelLogEntry;
use Carbon\Carbon;
use App\Exports\FuelLogEntriesExport;
use Maatwebsite\Excel\Facades\Excel;

class FuelLogEntryController extends Controller
{
    /*
    *   index
    */
    public function index()
    {
        #View the form
        return redirect('fuelConsumptionCalculator.form');
    }

    /*
     * GET  showForm
     */
    public function showForm(Request $request)
    {
        # set variables from session or use defaults
        $startDistance = $request->session()->get('startDistance', 0);
        $endDistance = $request->session()->get('endDistance', 0);
        $fuelVolume = $request->session()->get('fuelVolume', 0);
        $distanceUnit = $request->session()->get('distanceUnit', '');
        $volumeUnit = $request->session()->get('volumeUnit', '');
        $fuelConsumed = $request->session()->get('fuelConsumed', 0);
        $distance = $request->session()->get('distance', 0);

        #if the user is logged in get the last entered end distance as the default start distance
        $lastEndDistance = '';
        $user = $request->user();
        if ($user != null) {
            $lastEndDistance = FuelLogEntry::where('user_id', '=', $user->id)->orderBy('created_at', 'desc')->limit(1)->pluck('end_distance');
            //dd($lastEndDistance);
            if(count($lastEndDistance)>0)
                $lastEndDistance = $lastEndDistance[0];
        }
        $startDistance = $lastEndDistance;

        #return the form view with these value
        return view('fuelConsumptionCalculator.form')->with([
            'startDistance' => $startDistance,
            'endDistance' => $endDistance,
            'fuelVolume' => $fuelVolume,
            'distanceUnit' => $distanceUnit,
            'volumeUnit' => $volumeUnit,
            'fuelConsumed' => $fuelConsumed,
            'distance' => $distance,
            'logged_in_user' => $user,
        ]);
    }

    /*
     * POST formProcess
     */
    public function formProcess(Request $request)
    {
        # Validate the form request
        $request->validate([
            'startDistance' => 'required|numeric|min:1',
            'endDistance' => 'required|numeric|min:1|gt:startDistance',
            'fuelVolume' => 'required|numeric|min:1',
            'distanceUnit' => 'required',
            'volumeUnit' => 'required'
        ]);

        # Store the startDistance, endDistance and fuelVolume in variables
        # The second parameter (null) is what the variable
        # will be set to *if* the value is not in the request.

        $startDistance = $request->input('startDistance', null);
        $endDistance = $request->input('endDistance', null);
        $fuelVolume = $request->input('fuelVolume', null);
        $distanceUnit = $request->input('distanceUnit', null);
        $volumeUnit = $request->input('volumeUnit', null);
        $addLog = $request->input('addLog', null);

        #If startDistance, endDistance and fuelVolume are not null and the request has no errors calculate fuel consumed
        $distance = 0;
        $fuelConsumed = 0;
        if (!$request->hasErrors && $startDistance && $endDistance && $fuelVolume) {
            #calculate fuel consumed - this is intentionally without units so you could calculate miles/liter if desired
            $distance = (float)number_format($endDistance - $startDistance, 1, '.', '');
            $fuelConsumed = (float)number_format($distance / $fuelVolume, 1, '.', '');
        }

        #If the add log checkbox is checked add the data to the fuel log entries table
        #Note this checkbox is only visible if the user has registered and is logged in
        if ($addLog == "on") {
            #save the entry to the fuel log entries table
            $user = $request->user();
            $fuelLog = new FuelLogEntry();
            $fuelLog->fillup_date = Carbon::now();
            $fuelLog->start_distance = $startDistance;
            $fuelLog->end_distance = $endDistance;
            $fuelLog->distance_units = $distanceUnit;
            $fuelLog->fuel_volume = $fuelVolume;
            $fuelLog->fuel_units = $volumeUnit;
            $fuelLog->user_id = $user->id;

            # Invoke the Eloquent `save` method to generate a new row in the
            # `fuel logs` table, with the above data
            $fuelLog->save();

            return redirect('/fuelConsumptionCalculator/get-fuel-log');
        }

        # If the user did not login just perform the calculation and return to the form
        # Redirect the user page to the page that shows the form to show the calculation
        return redirect('/fuelConsumptionCalculator/show-form')->with([
            'fuelConsumed' => $fuelConsumed,
            'distance' => $distance,
            'startDistance' => $startDistance,
            'endDistance' => $endDistance,
            'fuelVolume' => $fuelVolume,
            'distanceUnit' => $request->get('distanceUnit'),
            'volumeUnit' => $request->get('volumeUnit'),
        ]);
    }
    /*
    *  /fuelConsumptionCalculator/get-fuel-log
    * GET fuel log entries for logged in user
   */
    public function getFuelLog(Request $request)
    {
        #get logged in user
        $user = $request->user();

        #initialize variables
        $log = null;
        $totalDistance = 0;
        $totalFuel = 0;
        $average = 0;

        # get fuel log entries for logged in user
        # get distance and fuel consumption as calculated columns
        if($user != null) {
            $log = $user->fuel_log_entries()->select(['*', \DB::raw('(end_distance-start_distance) AS distance, (end_distance-start_distance/fuel_volume) AS fuel_consumed')])->get();
            $totalDistance = $log->sum('distance');
            $totalFuel = $log->sum('fuel_volume');
            $average = (float)number_format($totalDistance / $totalFuel, 1, '.', '');
        }
        # view fuel log entries for the logged in user
        return view('fuelConsumptionCalculator.viewLog')->with([
            'fuelLog' => $log,
            'totalDistance' => $totalDistance,
            'totalFuel' => $totalFuel,
            'average' => $average,
        ]);
    }

    /*
    * GET /fuelConsumptionCalculator/edit-fuel-log-entry/{id}
    */
    public function edit($id)
    {
        $fuelLogEntry = FuelLogEntry::find($id);

        if (!$fuelLogEntry) {
            return redirect('/fuelConsumptionCalculator/get-fuel-log')->with([
                'alert' => '“Fuel log entry not found.'
            ]);
        }
        if (!Gate::allows('fuelLogEntries.manage', $fuelLogEntry)) {
            return redirect('/')->with(['alert' => 'Access denied.']);
        }

        return view('fuelConsumptionCalculator.edit')->with([
            'fuelLogEntry' => $fuelLogEntry,
        ]);
    }

    /*
     * PUT //fuelConsumptionCalculator/update-fuel-log-entry/{id}
    */
    public function update(Request $request, $id)
    {
        # Validate the form request
        $request->validate([
            'startDistance' => 'required|numeric|min:1',
            'endDistance' => 'required|numeric|min:1|gt:startDistance',
            'fuelVolume' => 'required|numeric|min:1',
            'distanceUnit' => 'required',
            'volumeUnit' => 'required'
        ]);

        # get the logged in user
        $user = $request->user();

        $fuelLogEntry = FuelLogEntry::find($id);
        $fuelLogEntry->start_distance = $request->get('startDistance');
        $fuelLogEntry->end_distance = $request->get('endDistance');
        $fuelLogEntry->distance_units = $request->get('distanceUnit');
        $fuelLogEntry->fuel_volume = $request->get('fuelVolume');
        $fuelLogEntry->fuel_units = $request->get('volumeUnit');
        $fuelLogEntry->user_id = $user->id;
        $fuelLogEntry->save();

        return redirect('/fuelConsumptionCalculator/get-fuel-log')->with([
            'alert' => 'Fuel log entry for date '. $fuelLogEntry->fillup_date . ' was updated.'
        ]);
    }

    /*
   * Ask user to confirm they want to delete the fuel log entry
   * GET /fuelConsumptionCalculator/delete/{id}
   */
    public function delete($id)
    {
        $fuelLogEntry = FuelLogEntry::find($id);
        if (!$fuelLogEntry) {
            return redirect('/fuelConsumptionCalculator.viewLog')->with(['alert' => 'Fuel log entry not found']);
        }
        if (!Gate::allows('fuelLogEntries.manage', $fuelLogEntry)) {
            return redirect('/')->with(['alert' => 'Access denied.']);
        }
        return view('fuelConsumptionCalculator.delete')->with([
            'fuelLogEntry' => $fuelLogEntry,
        ]);
    }
    /*
    * Deletes a fuel log entry
    * DELETE /fuelConsumptionCalculator/delete-fuel-log-entry/{id}
    */
    public function destroy($id)
    {
        $fuelLogEntry = FuelLogEntry::find($id);
        $fillupDate = $fuelLogEntry->fillup_date;
        $fuelLogEntry->delete();
        return redirect('/fuelConsumptionCalculator/get-fuel-log')->with([
            'alert' => 'Fuel log entry for date ' . $fillupDate . ' was removed.'
        ]);
    }
    /*
    * Exports a users  fuel log to excel
    * /export
    */
    public function export()
    {
        return Excel::download(new FuelLogEntriesExport, 'FuelLog.xlsx');
    }

    function excel()
    {
        #get fuel log entries for the logged in user
        $fuel_log_entries = Auth::user()->fuel_log_entries()->select(['*', \DB::raw('(end_distance-start_distance) AS distance, ((end_distance-start_distance)/fuel_volume) AS fuel_consumed')])->get();

        #create an array of the datea
        foreach($fuel_log_entries as $fuel_log_entry)
        {
            $fuel_log_array[] = array(
                'Date'  => $fuel_log_entry->fillup_date,
                'Distance'   => $fuel_log_entry->distance,
                'Fuel'    => $fuel_log_entry->fuel_volume,
                'Fuel Consumption'  => $fuel_log_entry->fuel_consumed,
            );
        }
        #use the excel facade to download the fuel log as excel
        Excel::download($fuel_log_array,'FuelLog.xlsx');
    }
}


