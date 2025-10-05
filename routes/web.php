<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MedicalSupplyController;
use App\Http\Controllers\DroneController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\DeliveryRequestController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HospitalPortalController;
use App\Http\Controllers\OperatorPortalController;

// ==================== PUBLIC ROUTES ====================

// Landing Page
Route::get('/', function () {
    // If user is logged in, redirect to dashboard
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('welcome');
})->name('home');

// Public Delivery Tracking (no auth required)
Route::get('/track', function () {
    return view('tracking.public');
})->name('tracking.public');

Route::get('/track/{trackingNumber}', function ($trackingNumber) {
    return view('tracking.show', compact('trackingNumber'));
})->name('tracking.show');

// ==================== AUTHENTICATION ROUTES ====================
// Include authentication routes from auth.php
require __DIR__.'/auth.php';

// ==================== AUTHENTICATED ROUTES ====================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // ==================== ADMIN DASHBOARD ====================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/realtime-stats', [AdminDashboardController::class, 'realtimeStats'])->name('dashboard.realtime');
        Route::get('/dashboard/export', [AdminDashboardController::class, 'export'])->name('dashboard.export');
        
        // ==================== MEDICAL SUPPLIES ====================
        Route::prefix('supplies')->name('supplies.')->group(function () {
            Route::get('/', [MedicalSupplyController::class, 'index'])->name('index');
            Route::get('/create', [MedicalSupplyController::class, 'create'])->name('create');
            Route::post('/', [MedicalSupplyController::class, 'store'])->name('store');
            Route::get('/{supply}', [MedicalSupplyController::class, 'show'])->name('show');
            Route::get('/{supply}/edit', [MedicalSupplyController::class, 'edit'])->name('edit');
            Route::put('/{supply}', [MedicalSupplyController::class, 'update'])->name('update');
            Route::delete('/{supply}', [MedicalSupplyController::class, 'destroy'])->name('destroy');
            
            // Stock Management
            Route::post('/{supply}/adjust-stock', [MedicalSupplyController::class, 'adjustStock'])->name('adjust-stock');
            
            // AJAX/API Endpoints
            Route::get('/alerts/low-stock', [MedicalSupplyController::class, 'lowStockAlert'])->name('alerts.low-stock');
            Route::get('/alerts/expiring', [MedicalSupplyController::class, 'expiringAlert'])->name('alerts.expiring');
            
            // Export
            Route::get('/export/csv', [MedicalSupplyController::class, 'export'])->name('export');
        });
        
        // ==================== DRONES ====================
        Route::prefix('drones')->name('drones.')->group(function () {
            Route::get('/', [DroneController::class, 'index'])->name('index');
            Route::get('/create', [DroneController::class, 'create'])->name('create');
            Route::post('/', [DroneController::class, 'store'])->name('store');
            Route::get('/{drone}', [DroneController::class, 'show'])->name('show');
            Route::get('/{drone}/edit', [DroneController::class, 'edit'])->name('edit');
            Route::put('/{drone}', [DroneController::class, 'update'])->name('update');
            Route::delete('/{drone}', [DroneController::class, 'destroy'])->name('destroy');
            
            // Status Management
            Route::post('/{drone}/update-status', [DroneController::class, 'updateStatus'])->name('update-status');
            Route::post('/{drone}/update-battery', [DroneController::class, 'updateBattery'])->name('update-battery');
            Route::post('/{drone}/update-position', [DroneController::class, 'updatePosition'])->name('update-position');
            
            // Maintenance
            Route::post('/{drone}/record-maintenance', [DroneController::class, 'recordMaintenance'])->name('record-maintenance');
            
            // AJAX/API Endpoints
            Route::get('/available/list', [DroneController::class, 'available'])->name('available');
            Route::get('/{drone}/tracking', [DroneController::class, 'tracking'])->name('tracking');
            
            // Export
            Route::get('/export/csv', [DroneController::class, 'export'])->name('export');
        });
        
        // ==================== HOSPITALS ====================
        Route::prefix('hospitals')->name('hospitals.')->group(function () {
            Route::get('/', [HospitalController::class, 'index'])->name('index');
            Route::get('/create', [HospitalController::class, 'create'])->name('create');
            Route::post('/', [HospitalController::class, 'store'])->name('store');
            Route::get('/{hospital}', [HospitalController::class, 'show'])->name('show');
            Route::get('/{hospital}/edit', [HospitalController::class, 'edit'])->name('edit');
            Route::put('/{hospital}', [HospitalController::class, 'update'])->name('update');
            Route::delete('/{hospital}', [HospitalController::class, 'destroy'])->name('destroy');
            
            // AJAX/API Endpoints
            Route::get('/nearby/search', [HospitalController::class, 'nearby'])->name('nearby');
            
            // Export
            Route::get('/export/csv', [HospitalController::class, 'export'])->name('export');
        });
        
        // ==================== DELIVERY REQUESTS ====================
        Route::prefix('delivery-requests')->name('delivery-requests.')->group(function () {
            Route::get('/', [DeliveryRequestController::class, 'index'])->name('index');
            Route::get('/create', [DeliveryRequestController::class, 'create'])->name('create');
            Route::post('/', [DeliveryRequestController::class, 'store'])->name('store');
            Route::get('/{deliveryRequest}', [DeliveryRequestController::class, 'show'])->name('show');
            Route::get('/{deliveryRequest}/edit', [DeliveryRequestController::class, 'edit'])->name('edit');
            Route::put('/{deliveryRequest}', [DeliveryRequestController::class, 'update'])->name('update');
            
            // Approval Workflow
            Route::post('/{deliveryRequest}/approve', [DeliveryRequestController::class, 'approve'])->name('approve');
            Route::post('/{deliveryRequest}/reject', [DeliveryRequestController::class, 'reject'])->name('reject');
            Route::post('/{deliveryRequest}/cancel', [DeliveryRequestController::class, 'cancel'])->name('cancel');
            
            // AJAX/API Endpoints
            Route::get('/pending/list', [DeliveryRequestController::class, 'pending'])->name('pending');
        });
        
        // ==================== DELIVERIES ====================
        Route::prefix('deliveries')->name('deliveries.')->group(function () {
            Route::get('/', [DeliveryController::class, 'index'])->name('index');
            Route::get('/create', [DeliveryController::class, 'create'])->name('create');
            Route::post('/', [DeliveryController::class, 'store'])->name('store');
            Route::get('/{delivery}', [DeliveryController::class, 'show'])->name('show');
            
            // Delivery Lifecycle
            Route::post('/{delivery}/start', [DeliveryController::class, 'start'])->name('start');
            Route::post('/{delivery}/mark-delivered', [DeliveryController::class, 'markAsDelivered'])->name('mark-delivered');
            Route::post('/{delivery}/complete', [DeliveryController::class, 'complete'])->name('complete');
            Route::post('/{delivery}/cancel', [DeliveryController::class, 'cancel'])->name('cancel');
            
            // AJAX/API Endpoints
            Route::get('/active/list', [DeliveryController::class, 'active'])->name('active');
            Route::get('/{delivery}/tracking', [DeliveryController::class, 'tracking'])->name('tracking');
            Route::post('/{delivery}/update-position', [DeliveryController::class, 'updatePosition'])->name('update-position');
        });
        
        // ==================== USERS ====================
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            
            // User Actions
            Route::post('/{user}/suspend', [UserController::class, 'suspend'])->name('suspend');
            Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        });
        
        // ==================== ROLES & PERMISSIONS ====================
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [RoleController::class, 'show'])->name('show');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });
    
    // ==================== HOSPITAL PORTAL ROUTES ====================
    Route::prefix('hospital')->name('hospital.')->middleware('role:hospital_admin,hospital_staff')->group(function () {
        
        // Hospital Dashboard
        Route::get('/dashboard', [HospitalPortalController::class, 'dashboard'])->name('dashboard');
        
        // Delivery Requests
        Route::get('/requests', [HospitalPortalController::class, 'requestsIndex'])->name('requests.index');
        Route::get('/requests/create', [HospitalPortalController::class, 'requestsCreate'])->name('requests.create');
        Route::post('/requests', [HospitalPortalController::class, 'requestsStore'])->name('requests.store');
        
        // Track Deliveries
        Route::get('/deliveries', [HospitalPortalController::class, 'deliveriesIndex'])->name('deliveries.index');
    });    // ==================== DRONE OPERATOR ROUTES ====================
    Route::prefix('operator')->name('operator.')->middleware('role:drone_operator')->group(function () {
        
        // Operator Dashboard
        Route::get('/dashboard', [OperatorPortalController::class, 'dashboard'])->name('dashboard');
        
        // Assigned Deliveries
        Route::get('/deliveries', [OperatorPortalController::class, 'deliveriesIndex'])->name('deliveries.index');
        Route::get('/deliveries/{delivery}', [OperatorPortalController::class, 'deliveriesShow'])->name('deliveries.show');
        
        // Delivery Actions
        Route::post('/deliveries/{delivery}/start', [OperatorPortalController::class, 'startDelivery'])->name('deliveries.start');
        Route::post('/deliveries/{delivery}/mark-delivered', [OperatorPortalController::class, 'markAsDelivered'])->name('deliveries.mark-delivered');
        Route::post('/deliveries/{delivery}/cancel', [OperatorPortalController::class, 'cancelDelivery'])->name('deliveries.cancel');
        Route::post('/deliveries/{delivery}/report-incident', [OperatorPortalController::class, 'reportIncident'])->name('deliveries.report-incident');
    });
    
    // ==================== USER PROFILE ====================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

// ==================== FALLBACK ROUTE ====================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
