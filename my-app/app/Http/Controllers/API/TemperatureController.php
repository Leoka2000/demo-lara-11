<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Events\BatteryTemperature;
use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    public function store(Request $request)
    {
        if ($request->bearerToken() !== env('API_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'timestamp' => 'required|integer',
            'temperature' => 'required|integer',
        ]);

        // Save to DB or handle data
        return response()->json(['success' => true]);
    }
}
