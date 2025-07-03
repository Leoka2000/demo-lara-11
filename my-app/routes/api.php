<?php

use Illuminate\Support\Facades\Route;
// For testing



use App\Http\Controllers\API\TemperatureController;

Route::post('/temperature', [TemperatureController::class, 'store']);
Route::get('/temperature', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'This endpoint only accepts POST requests for temperature data.'
    ]);
});
