<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric',
        ]);

        // Store or process temperature here
        // For testing, just return JSON response:
        return response()->json([
            'message' => 'Temperature stored successfully',
            'temperature' => $validated['temperature'],
        ]);
    }
}
