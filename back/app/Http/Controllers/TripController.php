<?php

namespace App\Http\Controllers;

use App\Events\TripAccepted;
use App\Events\TripCreated;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required'
        ]);

        $trip = $request->user()->trips()->create($validated);
        TripCreated::dispatch($trip);
        return $trip;
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip, Request $request)
    {
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }
        if ($trip->driver->id && $request->user()->driver->id) {
            if ($trip->driver->id === $request->user()->driver->id) {
                return $trip;
            }
        }
        return response()->json(['message' => 'Cannot find this trip'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function accept(Request $request, Trip $trip)
    {

        $request->validate([
            'driver_location' => 'required'
        ]);
        $trip->update([
            'driver_id' => $request->user()->driver->id,
            'driver_location' => $request->driver_location,
        ]);

        $trip->load('driver.user');
        TripAccepted::dispatch($trip);
        return $trip;
    }

    public function start(Request $request, Trip $trip)
    {

        $trip->update([
            'is_started' => true,
        ]);

        $trip->load('driver.user');
        TripStarted::dispatch($trip);
        return $trip;
    }


    public function end(Request $request, Trip $trip)
    {
        $trip->update([
            'is_complete' => true,
        ]);

        $trip->load('driver.user');
        TripEnded::dispatch($trip);
        return $trip;
    }


    public function location(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location,
        ]);

        $trip->load('driver.user');
        TripLocationUpdated::dispatch($trip);
        return $trip;
    }



}
