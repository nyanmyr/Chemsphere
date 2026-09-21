<?php

use App\Http\Controllers\AlertsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/alerts', [AlertsController::class, 'index']);
    Route::patch('/alerts/{alert}/read', [AlertsController::class, 'markAsRead']);
});
