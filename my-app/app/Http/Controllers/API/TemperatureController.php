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
        $validated = $request->validate([
            'temperature' => 'required',
            'timestamp' => 'required'
        ]);
        Log::info('TemperatureController received data', $validated);

        event(new BatteryTemperature(
            $validated['temperature'],
            $validated['timestamp']
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'Temperature data broadcasted'
        ]);
    }
}
