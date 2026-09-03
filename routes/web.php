<?php

use App\EquipmentStatus;
use App\GHSSymbol;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChemicalsController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\UsersController;
use App\Models\Chemical;
use App\Models\Equipment;
use App\Models\Location;
use App\SafetyClass;
use App\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

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

// Standard Auth Actions
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google SSO Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// locations routes
Route::get('/locations', [LocationsController::class, 'locations'])
->middleware('auth')
->name('locations');

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
->middleware(['auth', 'admin'])
->name('locations.delete');

Route::get('/locations/{id}/edit', [LocationsController::class, 'edit'])
->middleware(['auth', 'admin'])
->name('locations.edit');

Route::put('/locations/{id}', [LocationsController::class, 'update'])
->middleware(['auth', 'admin'])
->name('locations.update');

// inventory routes
Route::get('/inventory', [ChemicalsController::class, 'chemicals'])
->middleware('auth')
->name('inventory');

Route::get('/inventory/create', function () {
    return view('create_chemical');
})
->middleware(['auth', 'admin'])
->name('inventory.create');

Route::post('/inventory/create', function () {
    $user = Auth::user();

    $validated = request()->validate([
        'location_id' => 'required|integer|exists:locations,location_id',
        'chemical_name'   => 'required|string|max:255',
        'batch_number'   => 'required|string|max:255',
        'brand_name'   => 'required|string|max:255',
        'volume_per_unit'   => 'required|numeric|min:0|max:9999997.999',
        'initial_quantity'   => 'required|numeric|min:0|max:9999997.999',
        'current_quantity'   => 'required|numeric|min:0|max:9999997.999',
        'expiration_date'   => 'required|date',
        'arrival_date'   => 'required|date',
        'safety_classes'   => 'nullable|array',
        'safety_classes.*' => ['required', Rule::enum(SafetyClass::class)],
        'ghs_symbols'   => 'nullable|array',
        'ghs_symbols.*' => ['required', Rule::enum(GHSSymbol::class)],
        'unit' => ['required', Rule::enum(Unit::class)],
    ]);

    $validated['safety_classes'] = implode(',', $validated['safety_classes'] ?? []);
    $validated['ghs_symbols'] = implode(',', $validated['ghs_symbols'] ?? []);
    $validated['created_by'] = $user['user_id'];

    Chemical::create($validated);

    return redirect()->route('inventory');
})
->middleware(['auth', 'admin'])
->name('inventory.store');

Route::delete('/inventory/{id}', [ChemicalsController::class, 'delete'])
->middleware(['auth', 'admin'])
->name('inventory.delete');

Route::get('/inventory/{id}/edit', [ChemicalsController::class, 'edit'])
->middleware(['auth', 'admin'])
->name('inventory.edit');

Route::put('/inventory/{id}', [ChemicalsController::class, 'update'])
->middleware(['auth', 'admin'])
->name('inventory.update');

// locations routes
Route::get('/equipment', [EquipmentController::class, 'equipment'])
->middleware('auth')
->name('equipment');

Route::get('/equipment/create', function () {
    return view('create_equipment');
})
->middleware(['auth', 'admin'])
->name('equipment.create');

Route::post('/equipment/create', function () {
    $user = Auth::user();

    $validated = request()->validate([
        'location_id' => 'required|integer|exists:locations,location_id',
        'equipment_name'   => 'required|string|max:255',
        'model'   => 'required|string|max:255',
        'serial_id'   => 'required|string|max:255',
        'status' => ['required', Rule::enum(EquipmentStatus::class)],
        'quantity'   => 'required|numeric|min:0|max:9999997.999',
        'purchase_date'   => 'required|date',
        'warranty_expiration'   => 'required|date',
        'last_maintenance'   => 'required|date',
        'next_maintenance'   => 'required|date'
    ]);

    $validated['created_by'] = $user['user_id'];

    Equipment::create($validated);

    return redirect()->route('equipment');
})
->middleware(['auth', 'admin'])
->name('equipment.store');

Route::delete('/equipment/{id}', [EquipmentController::class, 'delete'])
->middleware(['auth', 'admin'])
->name('equipment.delete');

Route::get('/equipment/{id}/edit', [EquipmentController::class, 'edit'])
->middleware(['auth', 'admin'])
->name('equipment.edit');

Route::put('/equipment/{id}', [EquipmentController::class, 'update'])
->middleware(['auth', 'admin'])
->name('equipment.update');

// manage users routes
Route::get('/users', [UsersController::class, 'users'])
->middleware(['auth', 'admin'])
->name('users');

Route::get('/users/{id}/edit', [UsersController::class, 'edit'])
->middleware(['auth', 'admin'])
->name('users.edit');

Route::put('/users/{id}', [UsersController::class, 'update'])
->middleware(['auth', 'admin'])
->name('users.update');
