<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use App;
use App\FuelLogEntry;
use App\Exports\FuelLogEntriesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Actions\FuelLogEntry\LastDistanceEntered;
use App\Actions\FuelLogEntry\StoreFuelLogEntry;
use App\Actions\FuelLogEntry\UpdateFuelLogEntry;
use App\Actions\FuelLogEntry\FuelConsumptionCalculation;

class FuelLogEntryController extends Controller
{
    /*
    *   index
    */
    public function index()
    {
        # View the form
        return redirect('fuelConsumptionCalculator.form');
    }

    /*
     * GET  showForm
     */
    public function showForm(Request $request)
    {
        # set variables from session or use defaults
        $fillupDate = $request->session()->get('fillupDate', '');
        $startDistance = $request->session()->get('startDistance', '');
        $endDistance = $request->session()->get('endDistance', '');
        $fuelVolume = $request->session()->get('fuelVolume', '');
        $distanceUnit = $request->session()->get('distanceUnit', '');
        $volumeUnit = $request->session()->get('volumeUnit', '');
        $fuelConsumed = $request->session()->get('fuelConsumed', '');
        $distance = $request->session()->get('distance', '');

        # if the user is logged in get the last entered end distance as the default start distance
        $user = $request->user();

        if (!is_null($user)) {
            # if not start distance has been entered use last end distance as start distance
            if ($startDistance == '') {
                $action = new LastDistanceEntered($user);
                $startDistance = $action->getLastEndDistance();
            }
        }

        # default to todays date
        if($fillupDate == '')
            $fillupDate = Carbon::now()->format('Y-m-d');

        # return the form view with these value
        return view('fuelConsumptionCalculator.form')->with([
            'fillupDate' => $fillupDate,
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
            'fillupDate' => 'required|date',
            'startDistance' => 'required|numeric|min:1|max:900000',
            'endDistance' => 'required|numeric|min:1|max:999999|gt:startDistance',
            'fuelVolume' => 'required|numeric|min:1|max:1000',
            'distanceUnit' => 'required',
            'volumeUnit' => 'required'
        ]);

        # Store the startDistance, endDistance and fuelVolume in variables
        # The second parameter (null) is what the variable
        # will be set to *if* the value is not in the request.

        $fillupDate = $request->input('fillupDate', Carbon::now());
        $startDistance = $request->input('startDistance', null);
        $endDistance = $request->input('endDistance', null);
        $fuelVolume = $request->input('fuelVolume', null);
        $distanceUnit = $request->input('distanceUnit', null);
        $volumeUnit = $request->input('volumeUnit', null);
        $addLog = $request->input('addLog', null);

        # If the add log checkbox is checked add the data to the fuel log entries table
        # Note this checkbox is only visible if the user has registered and is logged in
        if ($addLog == "on") {

            $action = new StoreFuelLogEntry($request->only('fillupDate', 'startDistance', 'endDistance', 'fuelVolume', 'distanceUnit', 'volumeUnit'), $request->user());

            return redirect('/fuelConsumptionCalculator/get-fuel-log');
        }

        #If startDistance, endDistance and fuelVolume are not null and the request has no errors calculate fuel consumed
        $distance = $endDistance - $startDistance;
        $fuelConsumed = '';

        if (!is_null($startDistance) && !is_null($endDistance) && !is_null($fuelVolume)) {
            $fuelCalculation = new FuelConsumptionCalculation($request->only('fillupDate', 'startDistance', 'endDistance', 'distanceUnit', 'fuelVolume', 'volumeUnit'), $request->user());
            $fuelConsumed = $fuelCalculation->fuel_consumed;
        }

        # If the user did not login just perform the calculation and return to the form
        # Redirect the user page to the page that shows the form to show the calculation
        return redirect('/fuelConsumptionCalculator/show-form')->with([
            'fuelConsumed' => $fuelConsumed,
            'distance' => $distance,
            'fillupDate' => $fillupDate,
            'startDistance' => $startDistance,
            'endDistance' => $endDistance,
            'fuelVolume' => $fuelVolume,
            'distanceUnit' => $distanceUnit,
            'volumeUnit' => $volumeUnit,
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
        if ($user != null) {
            $log = $user->fuel_log_entries()->select(['*', \DB::raw('(end_distance-start_distance) AS distance, (end_distance-start_distance/fuel_volume) AS fuel_consumed')])->get();
            if (count($log) > 0) {
                $totalDistance = $log->sum('distance');
                $totalFuel = $log->sum('fuel_volume');
                $average = (float)number_format($totalDistance / $totalFuel, 1, '.', '');
            } else {
                # if not log entries are found
                return redirect('/')->with(['alert' => 'Fuel Log is empty, use form below to start a log',
                ]);
            }
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

        # check if the entry was found and that the current logged in user is authorized to update this entry
        # if not redirect back to fuel log if not found and to form if not authorized
        if (!$fuelLogEntry) {
            return redirect('/fuelConsumptionCalculator/get-fuel-log')->with([
                'alert' => 'Fuel log entry not found.'
            ]);
        }
        if (!Gate::allows('fuelLogEntries.manage', $fuelLogEntry)) {
            return redirect('/')->with(['alert' => 'Access denied.']);
        }

        # if the logged in user is authorized and the record with the passed submit the form
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
            'fillupDate' => 'required|date',
            'startDistance' => 'required|numeric|min:1|max:900000',
            'endDistance' => 'required|numeric|min:1|gt:startDistance|max:999999',
            'fuelVolume' => 'required|numeric|min:1|max:1000',
            'distanceUnit' => 'required',
            'volumeUnit' => 'required'
        ]);
        # get the fuel log entry
        $fuelLogEntry = FuelLogEntry::find($id);

        # get the logged in user
        $user = $request->user();

        # perform the update
        $action = new UpdateFuelLogEntry($request->only('fillupDate', 'startDistance', 'endDistance', 'fuelVolume', 'distanceUnit', 'volumeUnit'), $fuelLogEntry, $request->user());

        # redirect to fuel log with message reflecting the record was updated
        return redirect('/fuelConsumptionCalculator/get-fuel-log')->with([
            'alert' => 'Fuel log entry for date ' . $action->fillup_date . ' was updated.'
        ]);
    }

    /*
   * Ask user to confirm they want to delete the fuel log entry
   * GET /fuelConsumptionCalculator/delete/{id}
   */
    public function delete($id)
    {
        $fuelLogEntry = FuelLogEntry::find($id);
        # if the entry with the passed is not found  return to the fuel log with message not found
        # if or the current logged in user is not authorized to delete redirect to the form with error message
        if (!$fuelLogEntry) {
            return redirect('/fuelConsumptionCalculator.viewLog')->with(['alert' => 'Fuel log entry not found']);
        }
        if (!Gate::allows('fuelLogEntries.manage', $fuelLogEntry)) {
            return redirect('/')->with(['alert' => 'Access denied.']);
        }
        # if record is found and the currently logged in user is authorized to delete, delete this record
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
        # find the record
        $fuelLogEntry = FuelLogEntry::find($id);
        # save the fillup date for the return message before deleting
        $fillupDate = $fuelLogEntry->fillup_date;
        # delete the recored
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
        #export a view of the fuel log
        return Excel::download(new FuelLogEntriesExport, 'FuelLog.xlsx');
    }
}


