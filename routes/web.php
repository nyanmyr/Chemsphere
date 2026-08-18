<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChemicalsController;
use App\Http\Controllers\LocationsController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/pending', function () {
    return view('pending');
})->name('pending');

Route::get('/inventory', [ChemicalsController::class, 'chemicals'])
->middleware('auth')
->name('inventory');

Route::get('/locations', [LocationsController::class, 'locations'])
->middleware('auth')
->name('locations');

// Standard Auth Actions
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google SSO Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
