<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlaceController extends Controller
{
    /**
     * Autocomplete places using Google Maps API
     */
    public function autocomplete(Request $request)
    {
        $request->validate([
            'input' => 'required|string',
        ]);

        $apiKey = config('services.google.maps_api_key', 'AIzaSyAjK4gQ_-XPPBdO4h_0Mz481pY6vpnMkAQ');

        $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
            'input' => $request->input,
            'key' => $apiKey,
            'components' => 'country:id',
        ]);

        return response()->json($response->json());
    }

    /**
     * Get place details using Google Maps API
     */
    public function details(Request $request)
    {
        $request->validate([
            'place_id' => 'required|string',
        ]);

        $apiKey = config('services.google.maps_api_key', 'AIzaSyAjK4gQ_-XPPBdO4h_0Mz481pY6vpnMkAQ');

        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $request->place_id,
            'key' => $apiKey,
        ]);

        return response()->json($response->json());
    }
}