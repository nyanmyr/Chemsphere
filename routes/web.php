<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChemicalsController;
use App\Http\Controllers\LocationsController;
use App\Models\Location;

// user routes
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

// needs authentication routes
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

// model routes
Route::get('/locations/create', function () {
    return view('create_location');
})
->middleware(['auth', 'admin'])
->name('locations.create');

Route::post('/locations/create', function () {
    $validated = request()->validate([
        'location_name' => 'required|string|max:255',
        'description'   => 'nullable|string',
    ]);
    Location::create($validated);

    return redirect()->route('locations');
})
->middleware(['auth', 'admin'])
->name('locations.store');

Route::delete('/locations/{id}', [LocationsController::class, 'delete'])
->name('locations.delete');
