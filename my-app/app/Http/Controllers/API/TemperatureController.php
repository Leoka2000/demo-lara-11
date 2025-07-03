<?php

namespace App\Http\Controllers\API;

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    public function getDummy(Request $request)
    {
        $temperature = $request->query('temperature');
        $timestamp = $request->query('timestamp');

        return response()->json([
            'temperature' => $temperature,
            'timestamp' => $timestamp,
            'message' => 'Received from BLE',
        ]);
    }

    // Keep your existing store() if needed for real POST later
}
