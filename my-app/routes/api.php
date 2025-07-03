<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TemperatureController;

// For testing


// For real data from Python script
Route::post('temperature', [TemperatureController::class, 'store']);
