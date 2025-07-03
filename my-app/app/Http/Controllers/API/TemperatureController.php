<?php

namespace App\Http\Controllers\API;

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class TemperatureController extends Controller
{
    public function getDummy(): JsonResponse
    {
        // Generate dummy temperature between 20 and 40
        $temperature = round(mt_rand(2000, 4000) / 100, 2);

        return response()->json([
            'temperature' => $temperature,
            'message' => 'Dummy temperature fetched successfully',
        ]);
    }

    // Keep your existing store() if needed for real POST later
}
