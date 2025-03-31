<?php

namespace App\Listeners;

use App\Models\TaxiDriver;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\trackingDriverCurrentLocation;

class UpdateDriverLocation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(trackingDriverCurrentLocation $event)
    {
        // dd($event);
        // dd('ok');
        // dd($event->location);
        // Get the location data from the event
        $location = $event->location;
        // dd($location['current_location']);

        // $currentLocation = json_encode($location['current_location'], true);
        // $currentLocation = $location['current_location'];

        // dd($currentLocation);
        // Find the taxi driver by ID
        $taxiDriver = TaxiDriver::where('id', $location['driver_id'])->first();
        // dd($taxiDriver);

        if ($taxiDriver) {
            // Update the latitude and longitude fields
            $taxiDriver->update([
                'latitude' => $location['current_location']['lat'],
                'longitude' => $location['current_location']['long'],
            ]);
        }
    }
}
