<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TemperatureController;

Route::post('temperature', [TemperatureController::class, 'store']);
