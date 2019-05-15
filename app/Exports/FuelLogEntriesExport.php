<?php

namespace App\Exports;

use App\FuelLogEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class FuelLogEntriesExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $export = Auth::user()->fuel_log_entries()->select(['fillup_date', 'fuel_volume', 'fuel_units', 'start_distance', 'end_distance', 'distance_units', \DB::raw('(end_distance-start_distance) AS distance, CAST(((end_distance-start_distance)/fuel_volume) AS decimal(10,1)) AS fuel_consumed')])->get();
        return $export;
    }

    public function headings(): array
    {
        return [
            'Fillup Date',
            'Fuel Volume',
            'Fuel Units',
            'Start Distance',
            'End Distance',
            'Distance Units',
            'Distance (since last fillup)',
            'Fuel Consumed (since last fillup',
        ];
    }
}
