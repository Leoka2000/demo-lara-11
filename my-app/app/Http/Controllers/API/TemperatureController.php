<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Temperature;

class TemperatureController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'timestamp'   => 'required|numeric'
        ]);

        // Optionally log
        Log::info('Storing temperature data', $validated);

        // Save to database
        Temperature::create([
            'temperature' => $validated['temperature'],
            'timestamp'   => $validated['timestamp']
        ]);

        return response()->json(['message' => 'Temperature stored successfully']);
    }
}
