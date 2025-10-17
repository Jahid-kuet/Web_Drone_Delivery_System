<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeliveryTrackingController;
use App\Http\Controllers\Api\DroneController as ApiDroneController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ==================== PUBLIC API ROUTES ====================

// Public Delivery Tracking (no auth required)
Route::prefix('v1/public')->name('api.public.')->group(function () {
    
    // Track delivery by tracking number
    Route::get('/track/{trackingNumber}', [DeliveryTrackingController::class, 'track'])
        ->name('track');
    
    // Real-time position updates
    Route::get('/track/{trackingNumber}/realtime', [DeliveryTrackingController::class, 'realtimePosition'])
        ->name('track.realtime');
});

// ==================== AUTHENTICATED API ROUTES ====================

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // ==================== USER INFO ====================
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('roles', 'hospital'),
        ]);
    })->name('api.user');
    
    // ==================== DELIVERY TRACKING ====================
    Route::prefix('deliveries')->name('api.deliveries.')->group(function () {
        
        // Get all active deliveries
        Route::get('/active', [DeliveryTrackingController::class, 'activeDeliveries'])
            ->name('active');
        
        // Track specific delivery
        Route::get('/track/{trackingNumber}', [DeliveryTrackingController::class, 'track'])
            ->name('track');
        
        // Real-time position
        Route::get('/track/{trackingNumber}/realtime', [DeliveryTrackingController::class, 'realtimePosition'])
            ->name('realtime');
        
        // Update delivery position (for drone/pilot apps)
        Route::post('/track/{trackingNumber}/position', [DeliveryTrackingController::class, 'updatePosition'])
            ->name('update-position');
    });
    
    // ==================== DRONE MANAGEMENT ====================
    Route::prefix('drones')->name('api.drones.')->group(function () {
        
        // Get available drones
        Route::get('/available', [ApiDroneController::class, 'available'])
            ->name('available');
        
        // Get drone status
        Route::get('/{droneId}/status', [ApiDroneController::class, 'status'])
            ->name('status');
        
        // Update drone battery level
        Route::post('/{droneId}/battery', [ApiDroneController::class, 'updateBattery'])
            ->name('update-battery');
        
        // Update drone position
        Route::post('/{droneId}/position', [ApiDroneController::class, 'updatePosition'])
            ->name('update-position');
    });
    
    // ==================== NOTIFICATIONS ====================
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        
        // Get user notifications (paginated)
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');
        
        // Get unread count
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
            ->name('unread-count');
        
        // Mark notification as read
        Route::post('/{notificationId}/read', [NotificationController::class, 'markAsRead'])
            ->name('mark-read');
        
        // Mark all notifications as read
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])
            ->name('mark-all-read');
        
        // Delete notification
        Route::delete('/{notificationId}', [NotificationController::class, 'destroy'])
            ->name('destroy');
    });

    // ==================== DELIVERY CONFIRMATION (OTP & PHOTO) ====================
    Route::prefix('deliveries')->name('api.deliveries.confirmation.')->group(function () {
        
        // Generate OTP for delivery
        Route::post('/{deliveryId}/otp/generate', [\App\Http\Controllers\DeliveryConfirmationController::class, 'generateOTP'])
            ->name('otp.generate');
        
        // Verify OTP
        Route::post('/{deliveryId}/otp/verify', [\App\Http\Controllers\DeliveryConfirmationController::class, 'verifyOTP'])
            ->name('otp.verify');
        
        // Get OTP status
        Route::get('/{deliveryId}/otp/status', [\App\Http\Controllers\DeliveryConfirmationController::class, 'getOTPStatus'])
            ->name('otp.status');
        
        // Resend OTP
        Route::post('/{deliveryId}/otp/resend', [\App\Http\Controllers\DeliveryConfirmationController::class, 'resendOTP'])
            ->name('otp.resend');
        
        // Upload delivery proof photo
        Route::post('/{deliveryId}/photo', [\App\Http\Controllers\DeliveryConfirmationController::class, 'uploadPhoto'])
            ->name('photo.upload');
        
        // Upload recipient signature
        Route::post('/{deliveryId}/signature', [\App\Http\Controllers\DeliveryConfirmationController::class, 'uploadSignature'])
            ->name('signature.upload');
        
        // Complete delivery confirmation (OTP + Photo + Signature)
        Route::post('/{deliveryId}/confirm', [\App\Http\Controllers\DeliveryConfirmationController::class, 'completeConfirmation'])
            ->name('complete');
        
        // Get confirmation details
        Route::get('/{deliveryId}/confirmation', [\App\Http\Controllers\DeliveryConfirmationController::class, 'getConfirmationDetails'])
            ->name('details');
    });
    Route::prefix('delivery-confirmation')->name('api.confirmation.')->group(function () {
        
        // Generate OTP for delivery
        Route::post('/{deliveryId}/otp/generate', [\App\Http\Controllers\DeliveryConfirmationController::class, 'generateOTP'])
            ->name('generate-otp');
        
        // Verify OTP
        Route::post('/{deliveryId}/otp/verify', [\App\Http\Controllers\DeliveryConfirmationController::class, 'verifyOTP'])
            ->name('verify-otp');
        
        // Get OTP status
        Route::get('/{deliveryId}/otp/status', [\App\Http\Controllers\DeliveryConfirmationController::class, 'getOTPStatus'])
            ->name('otp-status');
        
        // Resend OTP
        Route::post('/{deliveryId}/otp/resend', [\App\Http\Controllers\DeliveryConfirmationController::class, 'resendOTP'])
            ->name('resend-otp');
        
        // Upload delivery proof photo
        Route::post('/{deliveryId}/photo', [\App\Http\Controllers\DeliveryConfirmationController::class, 'uploadPhoto'])
            ->name('upload-photo');
        
        // Upload recipient signature
        Route::post('/{deliveryId}/signature', [\App\Http\Controllers\DeliveryConfirmationController::class, 'uploadSignature'])
            ->name('upload-signature');
        
        // Complete confirmation (OTP + Photo + Signature)
        Route::post('/{deliveryId}/complete', [\App\Http\Controllers\DeliveryConfirmationController::class, 'completeConfirmation'])
            ->name('complete');
        
        // Get confirmation details
        Route::get('/{deliveryId}', [\App\Http\Controllers\DeliveryConfirmationController::class, 'getConfirmationDetails'])
            ->name('details');
    });
    
    // ==================== MEDICAL SUPPLIES ====================
    Route::prefix('supplies')->name('api.supplies.')->group(function () {
        
        // List all supplies
        Route::get('/', function (Request $request) {
            $supplies = \App\Models\MedicalSupply::active()
                ->when($request->category, function ($q, $category) {
                    $q->where('category', $category);
                })
                ->get();
            
            return response()->json([
                'success' => true,
                'count' => $supplies->count(),
                'data' => $supplies,
            ]);
        })->name('index');
        
        // Get specific supply
        Route::get('/{supplyId}', function ($supplyId) {
            $supply = \App\Models\MedicalSupply::find($supplyId);
            
            if (!$supply) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supply not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $supply,
            ]);
        })->name('show');
        
        // Low stock alerts
        Route::get('/alerts/low-stock', function () {
            $supplies = \App\Models\MedicalSupply::lowStock()->active()->get();
            
            return response()->json([
                'success' => true,
                'count' => $supplies->count(),
                'data' => $supplies,
            ]);
        })->name('alerts.low-stock');
        
        // Expiring soon alerts
        Route::get('/alerts/expiring', function () {
            $supplies = \App\Models\MedicalSupply::expiringSoon()->active()->get();
            
            return response()->json([
                'success' => true,
                'count' => $supplies->count(),
                'data' => $supplies,
            ]);
        })->name('alerts.expiring');
    });
    
    // ==================== HOSPITALS ====================
    Route::prefix('hospitals')->name('api.hospitals.')->group(function () {
        
        // List all hospitals
        Route::get('/', function (Request $request) {
            $hospitals = \App\Models\Hospital::active()
                ->when($request->type, function ($q, $type) {
                    $q->where('type', $type);
                })
                ->get();
            
            return response()->json([
                'success' => true,
                'count' => $hospitals->count(),
                'data' => $hospitals,
            ]);
        })->name('index');
        
        // Get specific hospital
        Route::get('/{hospitalId}', function ($hospitalId) {
            $hospital = \App\Models\Hospital::find($hospitalId);
            
            if (!$hospital) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hospital not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $hospital,
            ]);
        })->name('show');
        
        // Find nearby hospitals
        Route::get('/nearby/search', function (Request $request) {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:1',
            ]);
            
            $radius = $validated['radius'] ?? 50;
            
            $hospitals = \App\Models\Hospital::active()
                ->withinRadius($validated['latitude'], $validated['longitude'], $radius)
                ->orderByDistance($validated['latitude'], $validated['longitude'])
                ->get();
            
            return response()->json([
                'success' => true,
                'count' => $hospitals->count(),
                'data' => $hospitals->map(function ($hospital) use ($validated) {
                    return [
                        'id' => $hospital->id,
                        'name' => $hospital->name,
                        'type' => $hospital->type,
                        'address' => $hospital->full_address,
                        'distance_km' => $hospital->distanceTo($validated['latitude'], $validated['longitude']),
                        'coordinates' => [
                            'latitude' => $hospital->latitude,
                            'longitude' => $hospital->longitude,
                        ],
                        'has_landing_pad' => $hospital->has_drone_landing_pad,
                        'phone' => $hospital->phone,
                    ];
                }),
            ]);
        })->name('nearby');
    });
    
    // ==================== DELIVERY REQUESTS ====================
    Route::prefix('delivery-requests')->name('api.delivery-requests.')->group(function () {
        
        // List delivery requests
        Route::get('/', function (Request $request) {
            $query = \App\Models\DeliveryRequest::with(['hospital', 'supply']);
            
            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            // Filter by priority
            if ($request->priority) {
                $query->where('priority', $request->priority);
            }
            
            // Filter by hospital
            if ($request->hospital_id) {
                $query->where('hospital_id', $request->hospital_id);
            }
            
            $requests = $query->latest()->paginate(20);
            
            return response()->json([
                'success' => true,
                'data' => $requests->items(),
                'pagination' => [
                    'total' => $requests->total(),
                    'per_page' => $requests->perPage(),
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                ],
            ]);
        })->name('index');
        
        // Get pending requests
        Route::get('/pending', function () {
            $requests = \App\Models\DeliveryRequest::pending()
                ->with(['hospital', 'supply'])
                ->orderByPriority('desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'count' => $requests->count(),
                'data' => $requests,
            ]);
        })->name('pending');
        
        // Create delivery request
        Route::post('/', function (Request $request) {
            $validated = $request->validate([
                'hospital_id' => 'required|exists:hospitals,id',
                'medical_supply_id' => 'required|exists:medical_supplies,id',
                'quantity_requested' => 'required|integer|min:1',
                'priority' => 'required|string|in:low,normal,high,urgent,emergency',
                'required_by_date' => 'required|date|after:now',
                'delivery_notes' => 'nullable|string',
            ]);
            
            $deliveryRequest = \App\Models\DeliveryRequest::create(array_merge($validated, [
                'requested_by_user_id' => auth()->id(),
                'status' => 'pending',
            ]));
            
            return response()->json([
                'success' => true,
                'message' => 'Delivery request created successfully',
                'data' => $deliveryRequest->load(['hospital', 'supply']),
            ], 201);
        })->name('store');
    });
    
    // ==================== STATISTICS & ANALYTICS ====================
    Route::prefix('stats')->name('api.stats.')->group(function () {
        
        // Dashboard statistics
        Route::get('/dashboard', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'deliveries' => [
                        'total' => \App\Models\Delivery::count(),
                        'active' => \App\Models\Delivery::whereIn('status', ['pending', 'in_transit'])->count(),
                        'completed_today' => \App\Models\Delivery::where('status', 'completed')->whereDate('created_at', today())->count(),
                    ],
                    'drones' => [
                        'total' => \App\Models\Drone::count(),
                        'available' => \App\Models\Drone::where('status', 'available')->count(),
                        'in_flight' => \App\Models\Drone::where('status', 'in_flight')->count(),
                    ],
                    'requests' => [
                        'pending' => \App\Models\DeliveryRequest::where('status', 'pending')->count(),
                        'approved' => \App\Models\DeliveryRequest::where('status', 'approved')->count(),
                    ],
                    'supplies' => [
                        'low_stock' => \App\Models\MedicalSupply::lowStock()->count(),
                        'expiring_soon' => \App\Models\MedicalSupply::expiringSoon()->count(),
                    ],
                ],
            ]);
        })->name('dashboard');
    });
});

// ==================== API VERSION 2 (Future) ====================
Route::prefix('v2')->middleware('auth:sanctum')->group(function () {
    // Future API endpoints
    Route::get('/status', function () {
        return response()->json([
            'version' => '2.0',
            'status' => 'under_development',
        ]);
    });
});

// ==================== HEALTH CHECK ====================
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0',
    ]);
})->name('api.health');
