<?php

namespace App\Http\Controllers;

use App\Events\TripAccepted;
use App\Events\TripCreated;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required|',
            'destination' => 'required|',
            'destination_name' => 'required|',
            'total_cost' => 'required'
        ]);

        $trip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

        //you may recalculate cost 
        $trip->transaction()->create($request->only(['total_cost']));

        $trip->load('transaction');

        TripCreated::dispatch($trip, $request->user());

        return $trip;

    }

    public function show(Request $request, Trip $trip)
    {
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }

        if ($trip->driver?->id === $request->user()->driver?->id) {
            return $trip;
        }

        return response()->json(['message' => 'Cannot find this trip'], 404);

    }

    public function accept(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->driver->id,
            'driver_location' => $request->driver_location
        ]);

        $trip->load(['driver.user', 'transaction']);

        TripAccepted::dispatch($trip, $trip->user);

        return $trip;

    }

    public function start(Request $request, Trip $trip)
    {
        $trip->update([
            'is_started' => true,
        ]);

        $trip->load(['driver.user', 'transaction']);

        TripStarted::dispatch($trip, $trip->user);

        return $trip;
    }

    public function end(Request $request, Trip $trip)
    {
        $trip->update([
            'is_complete' => true,
        ]);

        $trip->load(['driver.user', 'transaction']);

        TripEnded::dispatch($trip, $trip->user);

        return $trip;
    }

    public function location(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load(['driver.user', 'transaction']);

        TripLocationUpdated::dispatch($trip, $trip->user);

        return $trip;


    }
}
