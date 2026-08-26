<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::select('id', 'name')->orderBy('name')->get();

        return LocationResource::collection($locations);
    }
}
