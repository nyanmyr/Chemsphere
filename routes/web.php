<?php

use App\Enums\AuditAction;
use App\Enums\EquipmentStatus;
use App\Enums\GHSSymbol;
use App\Enums\SafetyClass;
use App\Enums\Unit;
use App\Enums\UserRole;
use App\Http\Controllers\AlertsController;
use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChemicalsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\UsageLogsController;
use App\Http\Controllers\UsersController;
use App\Models\AuditLog;
use App\Models\Chemical;
use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

// user routes
Route::get('/', [DashboardController::class, 'welcome'])
->middleware('suspended')
->name('welcome');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/pending', function () {
    return view('pending');
})
->middleware(['verify', 'suspended'])
->name('pending');

Route::get('/suspended', function () {
    return view('suspended');
})
->middleware(['verify', 'pending'])
->name('suspended');

// Standard Auth Actions
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google SSO Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// locations routes
Route::get('/locations', [LocationsController::class, 'locations'])
->middleware(['auth', 'pending', 'suspended'])
->name('locations');

Route::get('/locations/create', function () {
    return view('create_location');
})
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('locations.create');

Route::post('/locations/create', function () {
    $user = Auth::user();

    $validated = request()->validate([
        'location_name' => 'required|string|max:255',
        'description'   => 'nullable|string',
    ]);

    $validated['created_by'] = $user['user_id'];

    Location::create($validated);

    AuditLog::create([
        'created_by' => $user['user_id'],
        'audit_action' => AuditAction::CREATE,
        'target' => 'create location',
    ]);

    return redirect()->route('locations')->with('success', __('messages.location.created'));
})
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('locations.store');

Route::delete('/locations/{id}', [LocationsController::class, 'delete'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('locations.delete');

Route::get('/locations/{id}/edit', [LocationsController::class, 'edit'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('locations.edit');

Route::put('/locations/{id}', [LocationsController::class, 'update'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('locations.update');

// Chemical inventory and batch/container routes
Route::get('/inventory', [ChemicalsController::class, 'index'])
    ->middleware(['auth', 'pending', 'suspended'])->name('inventory');
Route::get('/inventory/create', [ChemicalsController::class, 'create'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.create');
Route::post('/inventory', [ChemicalsController::class, 'store'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.store');
Route::get('/inventory/chemical/{chemical}', [ChemicalsController::class, 'show'])
    ->middleware(['auth', 'pending', 'suspended'])->name('inventory.show');
Route::get('/inventory/chemical/{chemical}/edit', [ChemicalsController::class, 'edit'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.chemical.edit');
Route::put('/inventory/chemical/{chemical}', [ChemicalsController::class, 'update'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.chemical.update');
Route::delete('/inventory/chemical/{chemical}', [ChemicalsController::class, 'destroy'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.delete');
Route::post('/inventory/chemical/{chemical}/containers', [ChemicalsController::class, 'storeContainer'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.containers.store');
Route::get('/inventory/containers/{container}/edit', [ChemicalsController::class, 'editContainer'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.containers.edit');
Route::put('/inventory/containers/{container}', [ChemicalsController::class, 'updateContainer'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.containers.update');
Route::delete('/inventory/containers/{container}', [ChemicalsController::class, 'destroyContainer'])
    ->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])->name('inventory.containers.delete');
Route::post('/inventory/containers/{container}/use', [ChemicalsController::class, 'useContainer'])
    ->middleware(['auth', 'pending', 'suspended'])->name('inventory.containers.use');

// equipment routes
Route::get('/equipment', [EquipmentController::class, 'equipment'])
->middleware(['auth', 'pending', 'suspended'])
->name('equipment');

Route::get('/equipment/create', function () {
    return view('create_equipment');
})
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('equipment.create');

Route::post('/equipment/create', function () {
    $user = Auth::user();

    $validated = request()->validate([
        'location_id' => 'required|integer|exists:locations,location_id',
        'equipment_name'   => 'required|string|max:255',
        'model'   => 'required|string|max:255',
        'serial_id'   => 'required|string|max:255',
        'uic_asset_code'   => 'required|string|max:255',
        'src_asset_code'   => 'required|string|max:255',
        'status' => ['required', Rule::enum(EquipmentStatus::class)],
        'date_received'   => 'required|date',
        'preventive_maintenance_date'   => 'required|date',
        'next_due'   => 'required|date'
    ]);

    $validated['created_by'] = $user['user_id'];

    Equipment::create($validated);

    AuditLog::create([
        'created_by' => $user['user_id'],
        'audit_action' => AuditAction::CREATE,
        'target' => 'create equipment',
    ]);

    return redirect()->route('equipment')->with('success', __('messages.equipment.created'));
})
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('equipment.store');

Route::delete('/equipment/{id}', [EquipmentController::class, 'delete'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('equipment.delete');

Route::get('/equipment/{id}/edit', [EquipmentController::class, 'edit'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('equipment.edit');

Route::put('/equipment/{id}', [EquipmentController::class, 'update'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('equipment.update');

Route::get('/equipment/use/{id}/edit', [EquipmentController::class, 'use_edit'])
->middleware(['auth', 'pending', 'suspended'])
->name('equipment.use.edit');

Route::put('/equipment/use/{id}', [EquipmentController::class, 'use_update'])
->middleware(['auth', 'pending', 'suspended'])
->name('equipment.use.update');

// manage users routes
Route::get('/users', [UsersController::class, 'users'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('users');

Route::get('/users/{id}/edit', [UsersController::class, 'edit'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('users.edit');

Route::put('/users/{id}', [UsersController::class, 'update'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('users.update');

// usage log routes
Route::get('/usagelogs', [UsageLogsController::class, 'usageLogs'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('usage_logs');

// audit log routes
Route::get('/auditlogs', [AuditLogsController::class, 'auditLogs'])
->middleware(['auth', 'role:' . UserRole::ADMIN->value, 'pending', 'suspended'])
->name('audit_logs');

// alert routes
Route::get('/alerts', [AlertsController::class, 'alerts'])
->middleware(['auth', 'pending', 'suspended'])
->name('alerts');

Route::patch('/alerts/{alert}/read', [AlertsController::class, 'markAsRead'])
->middleware(['auth', 'pending', 'suspended'])
->name('alerts.read');

Route::get('/alerts/unread-count', [AlertsController::class, 'unreadCount'])

->name('alerts.unread_count');
