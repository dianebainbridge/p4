<?php

/*
 * Misc "static" pages
 */
Route::view('/about', 'about');
Route::view('/contact', 'contact');
/*
 * Fuel Consumption Calculator
 */
#Show the form view
Route::view('/form', 'fuelConsumptionCalculator.form');
Route::get('/', 'FuelLogEntryController@showForm');
Route::get('/fuelConsumptionCalculator/show-form', 'FuelLogEntryController@showForm');

#Process the form
Route::post('/fuelConsumptionCalculator/form-process', 'FuelLogEntryController@formProcess');

# require login for routes that access and update the fuel log
Route::group(['middleware' => 'auth'], function () {

    #show the log
    Route::view('/viewLog', 'fuelConsumptionCalculator.viewLog');

    #get the fuel Log
    Route::get('/fuelConsumptionCalculator/get-fuel-log', 'FuelLogEntryController@getFuelLog');

    # Edit a fuel log entry in the fuel log entries table
    # Show the edit form for the fuel log entry with "id"
    Route::get('/fuelConsumptionCalculator/edit-fuel-log-entry/{id}', 'FuelLogEntryController@edit');
    # Process the form to edit a specific fuel log entry
    Route::put('/fuelConsumptionCalculator/update-fuel-log-entry/{id}', 'FuelLogEntryController@update');

    # Delete a fuel log entry from the fuel log entries table
    # Show the page to confirm deletion of a fuel log entry
    Route::get('/fuelConsumptionCalculator/delete/{id}', 'FuelLogEntryController@delete');
    # Process the deletion of a book
    Route::delete('/fuelConsumptionCalculator/delete-fuel-log-entry/{id}', 'FuelLogEntryController@destroy');

    # Logout form
    Route::view('/log-out', 'fuelConsumptionCalculator.logout');

    #export to excel
    Route::get('/export','FuelLogEntryController@export');

});
# routes associated with authentication
Auth::routes();

#route to view login-statsu - this route is not on any site page
Route::get('/show-login-status', function () {
    $user = Auth::user();

    if ($user) {
        dump('You are logged in.', $user->toArray());
    } else {
        dump('You are not logged in.');
    }

    return;
});
