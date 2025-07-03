<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TemperatureController;

Route::get('temperature', [TemperatureController::class, 'getDummy']);
