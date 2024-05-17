<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SPController;


Route::prefix('robot')->group(function () {
    Route::prefix('sp')->group(function () {
        Route::post('gov', [SPController::class, 'Gov']);
        // Route::post('pref', [SantanderController::class, 'Esteira']);
    });

    Route::prefix('mt')->group(function () {
        // Route::post('gov', [SantanderController::class, 'Esteira']);
    });

});
