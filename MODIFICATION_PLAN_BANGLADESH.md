# 🚀 **Drone Delivery System - Modification Plan for Bangladesh Hospitals**

## 📋 **Executive Summary**

Your system already has **excellent foundation** with:
- ✅ Role-based access (Admin, Hospital, Operator)
- ✅ Drone fleet management with telemetry
- ✅ Delivery tracking with GPS coordinates
- ✅ Hospital management with landing pads
- ✅ Medical supply inventory system
- ✅ API endpoints for tracking and notifications
- ✅ Database schema for deliveries, drones, tracking

**This document outlines specific modifications needed to make it production-ready for Bangladesh hospitals.**

---

## 🎯 **Required Modifications by Priority**

### **🔴 CRITICAL (Must Have - Week 1-2)**

#### **1. Bangladesh Location Validation** 
**Status**: ❌ Missing  
**Current**: Basic lat/lng validation (-90 to 90, -180 to 180)  
**Required**: Restrict all locations to Bangladesh boundaries

**Files to Modify:**

**A) Create Location Validation Service**
```php
// app/Services/BangladeshLocationService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BangladeshLocationService
{
    // Bangladesh bounding box
    const BANGLADESH_BOUNDS = [
        'min_lat' => 20.670883,
        'max_lat' => 26.631945,
        'min_lng' => 88.028336,
        'max_lng' => 92.673668,
    ];

    /**
     * Check if coordinates are within Bangladesh
     */
    public static function isInBangladesh(float $lat, float $lng): bool
    {
        // Fast bounding box check
        if ($lat < self::BANGLADESH_BOUNDS['min_lat'] || 
            $lat > self::BANGLADESH_BOUNDS['max_lat'] ||
            $lng < self::BANGLADESH_BOUNDS['min_lng'] || 
            $lng > self::BANGLADESH_BOUNDS['max_lng']) {
            return false;
        }

        return true;
    }

    /**
     * Validate and get location details using reverse geocoding
     */
    public static function validateLocation(float $lat, float $lng): array
    {
        // First check bounding box
        if (!self::isInBangladesh($lat, $lng)) {
            return [
                'valid' => false,
                'error' => 'Location must be within Bangladesh'
            ];
        }

        // Optional: Use Mapbox reverse geocoding for precise validation
        try {
            $token = config('services.mapbox.access_token');
            if ($token) {
                $response = Http::get("https://api.mapbox.com/geocoding/v5/mapbox.places/{$lng},{$lat}.json", [
                    'access_token' => $token,
                    'types' => 'country',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $country = $data['features'][0]['text'] ?? null;
                    
                    if ($country !== 'Bangladesh') {
                        return [
                            'valid' => false,
                            'error' => 'Location must be within Bangladesh',
                            'detected_country' => $country
                        ];
                    }
                }
            }

            return [
                'valid' => true,
                'latitude' => $lat,
                'longitude' => $lng
            ];

        } catch (\Exception $e) {
            // Fallback to bounding box check
            return [
                'valid' => true,
                'latitude' => $lat,
                'longitude' => $lng,
                'note' => 'Using bounding box validation'
            ];
        }
    }

    /**
     * Get Bangladesh divisions with coordinates
     */
    public static function getDivisions(): array
    {
        return [
            'dhaka' => ['lat' => 23.8103, 'lng' => 90.4125, 'name' => 'Dhaka'],
            'chittagong' => ['lat' => 22.3569, 'lng' => 91.7832, 'name' => 'Chittagong'],
            'rajshahi' => ['lat' => 24.3745, 'lng' => 88.6042, 'name' => 'Rajshahi'],
            'khulna' => ['lat' => 22.8456, 'lng' => 89.5403, 'name' => 'Khulna'],
            'barisal' => ['lat' => 22.7010, 'lng' => 90.3535, 'name' => 'Barisal'],
            'sylhet' => ['lat' => 24.8949, 'lng' => 91.8687, 'name' => 'Sylhet'],
            'rangpur' => ['lat' => 25.7439, 'lng' => 89.2752, 'name' => 'Rangpur'],
            'mymensingh' => ['lat' => 24.7471, 'lng' => 90.4203, 'name' => 'Mymensingh'],
        ];
    }
}
```

**B) Update Hospital Controller**  
File: `app/Http/Controllers/HospitalController.php`

Add to `store()` method after validation:
```php
// Validate Bangladesh location
$locationCheck = \App\Services\BangladeshLocationService::validateLocation(
    $validated['latitude'],
    $validated['longitude']
);

if (!$locationCheck['valid']) {
    return redirect()->back()
        ->withInput()
        ->with('error', $locationCheck['error'] ?? 'Invalid location for Bangladesh');
}
```

**C) Update Hospital Registration Form**  
File: `app/Http/Controllers/Auth/RegisterController.php` (if hospitals can self-register)

Add same validation in registration process.

**D) Add Validation Rule**  
File: `app/Rules/BangladeshLocation.php`

```php
<?php

namespace App\Rules;

use App\Services\BangladeshLocationService;
use Illuminate\Contracts\Validation\Rule;

class BangladeshLocation implements Rule
{
    protected $latitude;
    protected $longitude;

    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function passes($attribute, $value)
    {
        return BangladeshLocationService::isInBangladesh(
            $this->latitude,
            $this->longitude
        );
    }

    public function message()
    {
        return 'The location must be within Bangladesh.';
    }
}
```

**E) Update .env**
```env
# Add Mapbox token for reverse geocoding (optional but recommended)
MAPBOX_ACCESS_TOKEN=your_mapbox_token_here
```

**F) Update config/services.php**
```php
'mapbox' => [
    'access_token' => env('MAPBOX_ACCESS_TOKEN'),
],
```

---

#### **2. Regional Distribution Hubs**
**Status**: ❌ Missing (no hubs table)  
**Current**: Deliveries start from drones' current location  
**Required**: Multi-hub system with inventory

**A) Create Migration**
```bash
php artisan make:migration create_hubs_table
```

```php
// database/migrations/xxxx_create_hubs_table.php
Schema::create('hubs', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->string('hub_type')->default('warehouse'); // warehouse, distribution_center
    $table->string('address');
    $table->string('city');
    $table->string('division'); // Dhaka, Chittagong, etc.
    $table->string('postal_code');
    $table->decimal('latitude', 10, 8);
    $table->decimal('longitude', 11, 8);
    $table->string('contact_person');
    $table->string('phone');
    $table->string('email');
    $table->json('operating_hours')->nullable();
    $table->integer('storage_capacity_cubic_meters')->default(100);
    $table->boolean('has_cold_storage')->default(false);
    $table->decimal('cold_storage_temp_min', 5, 2)->nullable();
    $table->decimal('cold_storage_temp_max', 5, 2)->nullable();
    $table->integer('drone_charging_stations')->default(4);
    $table->integer('drone_parking_bays')->default(8);
    $table->boolean('has_maintenance_facility')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});

// Add hub_id to drones table
Schema::table('drones', function (Blueprint $table) {
    $table->foreignId('home_hub_id')->nullable()->constrained('hubs')->after('name');
    $table->foreignId('current_hub_id')->nullable()->constrained('hubs')->after('home_hub_id');
});

// Add hub_id to deliveries table
Schema::table('deliveries', function (Blueprint $table) {
    $table->foreignId('pickup_hub_id')->nullable()->constrained('hubs')->after('drone_id');
});
```

**B) Create Hub Model**
```php
// app/Models/Hub.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasGPSCoordinates;

class Hub extends Model
{
    use SoftDeletes, HasGPSCoordinates;

    protected $fillable = [
        'name', 'code', 'hub_type', 'address', 'city', 'division', 'postal_code',
        'latitude', 'longitude', 'contact_person', 'phone', 'email',
        'operating_hours', 'storage_capacity_cubic_meters', 'has_cold_storage',
        'cold_storage_temp_min', 'cold_storage_temp_max', 'drone_charging_stations',
        'drone_parking_bays', 'has_maintenance_facility', 'is_active'
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'has_cold_storage' => 'boolean',
        'has_maintenance_facility' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function drones()
    {
        return $this->hasMany(Drone::class, 'home_hub_id');
    }

    public function currentDrones()
    {
        return $this->hasMany(Drone::class, 'current_hub_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'pickup_hub_id');
    }

    public function inventories()
    {
        return $this->hasMany(HubInventory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithColdStorage($query)
    {
        return $query->where('has_cold_storage', true);
    }

    public function scopeInDivision($query, $division)
    {
        return $query->where('division', $division);
    }

    // Methods
    public function availableDrones()
    {
        return $this->currentDrones()->where('status', 'available');
    }

    public function findNearestHubForDelivery($hospitalLat, $hospitalLng)
    {
        return self::active()
            ->get()
            ->sortBy(function ($hub) use ($hospitalLat, $hospitalLng) {
                return $hub->distanceTo($hospitalLat, $hospitalLng);
            })
            ->first();
    }
}
```

**C) Create Hub Inventory**
```bash
php artisan make:migration create_hub_inventories_table
```

```php
Schema::create('hub_inventories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hub_id')->constrained()->onDelete('cascade');
    $table->foreignId('medical_supply_id')->constrained()->onDelete('cascade');
    $table->integer('quantity_available')->default(0);
    $table->integer('minimum_stock_level')->default(10);
    $table->integer('reorder_quantity')->default(50);
    $table->boolean('needs_cold_storage')->default(false);
    $table->decimal('storage_temperature_celsius', 5, 2)->nullable();
    $table->date('last_restocked_date')->nullable();
    $table->timestamps();
    
    $table->unique(['hub_id', 'medical_supply_id']);
});
```

**D) Seed Bangladesh Hubs**
```php
// database/seeders/HubSeeder.php
$hubs = [
    [
        'name' => 'Dhaka Central Hub',
        'code' => 'HUB-DHK-001',
        'hub_type' => 'warehouse',
        'address' => 'Tejgaon Industrial Area',
        'city' => 'Dhaka',
        'division' => 'Dhaka',
        'postal_code' => '1208',
        'latitude' => 23.7639,
        'longitude' => 90.3889,
        'contact_person' => 'Mohammed Rahman',
        'phone' => '+880-2-8870990',
        'email' => 'dhaka.hub@dronedelivery.bd',
        'has_cold_storage' => true,
        'cold_storage_temp_min' => 2.0,
        'cold_storage_temp_max' => 8.0,
        'drone_charging_stations' => 10,
        'drone_parking_bays' => 20,
        'has_maintenance_facility' => true,
        'is_active' => true,
    ],
    [
        'name' => 'Chittagong Hub',
        'code' => 'HUB-CTG-001',
        'hub_type' => 'distribution_center',
        'address' => 'Agrabad Commercial Area',
        'city' => 'Chittagong',
        'division' => 'Chittagong',
        'postal_code' => '4100',
        'latitude' => 22.3328,
        'longitude' => 91.8160,
        'contact_person' => 'Kamal Hossain',
        'phone' => '+880-31-710020',
        'email' => 'chittagong.hub@dronedelivery.bd',
        'has_cold_storage' => true,
        'cold_storage_temp_min' => 2.0,
        'cold_storage_temp_max' => 8.0,
        'drone_charging_stations' => 6,
        'drone_parking_bays' => 12,
        'has_maintenance_facility' => false,
        'is_active' => true,
    ],
    // Add Sylhet, Rajshahi, Khulna, etc.
];
```

---

#### **3. Emergency Priority System**
**Status**: ⚠️ Partially exists (priority field in delivery_requests)  
**Current**: Basic priority enum (normal, urgent, emergency)  
**Required**: Priority queue with automatic drone assignment

**Files to Modify:**

**A) Update DeliveryRequest Model**  
File: `app/Models/DeliveryRequest.php`

Add methods:
```php
public function isEmergency(): bool
{
    return $this->priority === 'emergency';
}

public function getPriorityScore(): int
{
    return match($this->priority) {
        'emergency' => 100,
        'urgent' => 50,
        'normal' => 10,
        default => 1,
    };
}

public function getMaxWaitTimeMinutes(): int
{
    return match($this->priority) {
        'emergency' => 15,  // 15 minutes max
        'urgent' => 60,     // 1 hour max
        'normal' => 240,    // 4 hours max
        default => 480,
    };
}
```

**B) Create Priority Queue Service**
```php
// app/Services/DeliveryPriorityQueue.php
<?php

namespace App\Services;

use App\Models\DeliveryRequest;
use App\Models\Drone;
use App\Models\Hub;
use Illuminate\Support\Collection;

class DeliveryPriorityQueue
{
    /**
     * Get pending delivery requests sorted by priority
     */
    public function getPendingQueue(): Collection
    {
        return DeliveryRequest::where('status', 'approved')
            ->whereDoesntHave('delivery') // Not yet assigned to delivery
            ->get()
            ->sortByDesc(function ($request) {
                // Sort by priority score and waiting time
                $priorityScore = $request->getPriorityScore();
                $waitingMinutes = $request->created_at->diffInMinutes(now());
                
                return ($priorityScore * 100) + $waitingMinutes;
            });
    }

    /**
     * Auto-assign next delivery to best available drone
     */
    public function autoAssignNext(): ?array
    {
        $nextRequest = $this->getPendingQueue()->first();
        
        if (!$nextRequest) {
            return null;
        }

        // Find nearest hub with available drones
        $hospital = $nextRequest->hospital;
        $nearestHub = Hub::active()
            ->whereHas('currentDrones', function ($q) {
                $q->where('status', 'available')
                  ->where('current_battery_level', '>=', 30);
            })
            ->get()
            ->sortBy(function ($hub) use ($hospital) {
                return $hub->distanceTo($hospital->latitude, $hospital->longitude);
            })
            ->first();

        if (!$nearestHub) {
            return null;
        }

        // Find best drone
        $drone = $nearestHub->availableDrones()
            ->where('current_battery_level', '>=', 30)
            ->first();

        if (!$drone) {
            return null;
        }

        return [
            'request' => $nextRequest,
            'hub' => $nearestHub,
            'drone' => $drone,
            'distance_km' => $nearestHub->distanceTo(
                $hospital->latitude,
                $hospital->longitude
            ),
        ];
    }
}
```

**C) Create Artisan Command for Auto-Assignment**
```bash
php artisan make:command AutoAssignDeliveries
```

```php
// app/Console/Commands/AutoAssignDeliveries.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DeliveryPriorityQueue;
use App\Models\Delivery;

class AutoAssignDeliveries extends Command
{
    protected $signature = 'deliveries:auto-assign';
    protected $description = 'Automatically assign pending deliveries to available drones';

    public function handle()
    {
        $queue = new DeliveryPriorityQueue();
        
        $assigned = 0;
        while ($assignment = $queue->autoAssignNext()) {
            // Create delivery
            $delivery = Delivery::create([
                'delivery_request_id' => $assignment['request']->id,
                'drone_id' => $assignment['drone']->id,
                'pickup_hub_id' => $assignment['hub']->id,
                'hospital_id' => $assignment['request']->hospital_id,
                'delivery_number' => 'DEL-' . now()->format('Ymd') . '-' . str_pad(Delivery::count() + 1, 4, '0', STR_PAD_LEFT),
                'tracking_number' => 'TRK-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'status' => 'preparing',
                'distance_km' => $assignment['distance_km'],
                'estimated_delivery_time' => now()->addMinutes($assignment['distance_km'] * 1.5), // ~1.5 min per km
            ]);

            $assignment['drone']->update(['status' => 'assigned']);
            
            $this->info("Assigned delivery {$delivery->delivery_number} to drone {$assignment['drone']->name}");
            $assigned++;
        }

        $this->info("Total deliveries auto-assigned: {$assigned}");
    }
}
```

**D) Schedule Auto-Assignment**  
File: `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Auto-assign deliveries every 5 minutes
    $schedule->command('deliveries:auto-assign')->everyFiveMinutes();
}
```

---

#### **4. Delivery Proof System (Photo/OTP)**
**Status**: ❌ Missing  
**Current**: No delivery confirmation mechanism  
**Required**: Photo upload + OTP verification

**A) Add Migration**
```bash
php artisan make:migration add_delivery_proof_to_deliveries_table
```

```php
Schema::table('deliveries', function (Blueprint $table) {
    $table->string('delivery_otp', 6)->nullable()->after('delivery_time');
    $table->timestamp('otp_generated_at')->nullable();
    $table->timestamp('otp_verified_at')->nullable();
    $table->string('proof_of_delivery_photo')->nullable();
    $table->string('recipient_signature_image')->nullable();
    $table->string('recipient_name')->nullable();
    $table->string('recipient_phone')->nullable();
    $table->text('delivery_notes')->nullable();
    $table->json('delivery_metadata')->nullable(); // GPS, timestamp, etc.
});
```

**B) Update Delivery Model**
```php
// app/Models/Delivery.php

public function generateOTP(): string
{
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    $this->update([
        'delivery_otp' => $otp,
        'otp_generated_at' => now(),
    ]);

    // Send OTP via SMS
    $this->sendOTPSMS();

    return $otp;
}

public function verifyOTP(string $otp): bool
{
    if ($this->delivery_otp !== $otp) {
        return false;
    }

    if ($this->otp_generated_at->addMinutes(30)->isPast()) {
        return false; // OTP expired
    }

    $this->update([
        'otp_verified_at' => now(),
    ]);

    return true;
}

public function confirmDelivery(array $data): bool
{
    // Upload photo if provided
    if (isset($data['photo'])) {
        $path = $data['photo']->store('delivery-proofs', 'public');
        $this->proof_of_delivery_photo = $path;
    }

    // Update delivery
    $this->update([
        'status' => 'delivered',
        'delivery_time' => now(),
        'recipient_name' => $data['recipient_name'] ?? null,
        'recipient_phone' => $data['recipient_phone'] ?? null,
        'delivery_notes' => $data['notes'] ?? null,
        'delivery_metadata' => json_encode([
            'confirmed_at' => now()->toIso8601String(),
            'confirmed_by_user_id' => auth()->id(),
            'gps_coordinates' => $data['gps'] ?? null,
        ]),
    ]);

    // Update drone status
    if ($this->drone) {
        $this->drone->update(['status' => 'available']);
    }

    return true;
}

protected function sendOTPSMS()
{
    // Integration with Bangladeshi SMS provider (e.g., BulkSMS BD, SSL Wireless)
    // Will implement in notification section
}
```

**C) Create Delivery Confirmation Controller**
```php
// app/Http/Controllers/Api/DeliveryConfirmationController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryConfirmationController extends Controller
{
    /**
     * Generate OTP for delivery
     */
    public function generateOTP(Request $request, $trackingNumber)
    {
        $delivery = Delivery::where('tracking_number', $trackingNumber)->firstOrFail();

        if ($delivery->status !== 'in_transit') {
            return response()->json([
                'success' => false,
                'message' => 'Delivery must be in transit to generate OTP',
            ], 400);
        }

        $otp = $delivery->generateOTP();

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to recipient',
            'otp_sent_to' => substr($delivery->deliveryRequest->recipient_phone, -4), // Show last 4 digits
        ]);
    }

    /**
     * Verify OTP and confirm delivery
     */
    public function verifyAndConfirm(Request $request, $trackingNumber)
    {
        $delivery = Delivery::where('tracking_number', $trackingNumber)->firstOrFail();

        $validated = $request->validate([
            'otp' => 'required|string|size:6',
            'photo' => 'required|image|max:5120', // 5MB max
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'gps' => 'nullable|array',
            'gps.latitude' => 'nullable|numeric|between:-90,90',
            'gps.longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Verify OTP
        if (!$delivery->verifyOTP($validated['otp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ], 400);
        }

        // Confirm delivery with photo
        $delivery->confirmDelivery($validated);

        return response()->json([
            'success' => true,
            'message' => 'Delivery confirmed successfully',
            'data' => [
                'delivery_number' => $delivery->delivery_number,
                'delivered_at' => $delivery->delivery_time,
                'recipient' => $delivery->recipient_name,
            ],
        ]);
    }

    /**
     * Upload proof photo (alternative to OTP)
     */
    public function uploadProof(Request $request, $trackingNumber)
    {
        $delivery = Delivery::where('tracking_number', $trackingNumber)->firstOrFail();

        $validated = $request->validate([
            'photo' => 'required|image|max:5120',
            'recipient_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $delivery->confirmDelivery($validated);

        return response()->json([
            'success' => true,
            'message' => 'Delivery proof uploaded successfully',
        ]);
    }
}
```

**D) Add API Routes**  
File: `routes/api.php`

```php
// Delivery confirmation endpoints
Route::prefix('deliveries/{trackingNumber}')->group(function () {
    Route::post('/generate-otp', [DeliveryConfirmationController::class, 'generateOTP']);
    Route::post('/verify-confirm', [DeliveryConfirmationController::class, 'verifyAndConfirm']);
    Route::post('/upload-proof', [DeliveryConfirmationController::class, 'uploadProof']);
});
```

---

### **🟡 HIGH PRIORITY (Important - Week 2-3)**

#### **5. Cold-Chain Monitoring**
**Status**: ⚠️ Partially exists (has_temperature_control in drones)  
**Current**: Boolean flag only, no real-time monitoring  
**Required**: Real-time temperature tracking with alerts

**A) Add Migration**
```bash
php artisan make:migration add_cold_chain_to_deliveries
```

```php
Schema::table('deliveries', function (Blueprint $table) {
    $table->boolean('requires_cold_chain')->default(false)->after('status');
    $table->decimal('target_temp_min_celsius', 5, 2)->nullable();
    $table->decimal('target_temp_max_celsius', 5, 2)->nullable();
    $table->decimal('current_cargo_temp_celsius', 5, 2)->nullable();
    $table->timestamp('temp_breach_detected_at')->nullable();
    $table->json('temperature_log')->nullable();
});

Schema::table('delivery_tracking', function (Blueprint $table) {
    $table->decimal('cargo_temperature_celsius', 5, 2)->nullable()->after('cargo_status');
    $table->boolean('temp_alert')->default(false);
});
```

**B) Update DeliveryTracking Model**
```php
public function checkTemperatureBreach(Delivery $delivery): bool
{
    if (!$delivery->requires_cold_chain) {
        return false;
    }

    $temp = $this->cargo_temperature_celsius;
    
    if ($temp < $delivery->target_temp_min_celsius || 
        $temp > $delivery->target_temp_max_celsius) {
        
        $this->update(['temp_alert' => true]);
        
        // Fire alert event
        event(new \App\Events\TemperatureBreachDetected($delivery, $this));
        
        return true;
    }

    return false;
}
```

**C) Create Temperature Breach Event**
```php
// app/Events/TemperatureBreachDetected.php
<?php

namespace App\Events;

use App\Models\Delivery;
use App\Models\DeliveryTracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TemperatureBreachDetected implements ShouldBroadcast
{
    public $delivery;
    public $telemetry;

    public function __construct(Delivery $delivery, DeliveryTracking $telemetry)
    {
        $this->delivery = $delivery;
        $this->telemetry = $telemetry;
    }

    public function broadcastOn()
    {
        return new Channel('delivery.' . $this->delivery->tracking_number);
    }

    public function broadcastWith()
    {
        return [
            'alert_type' => 'temperature_breach',
            'delivery_number' => $this->delivery->delivery_number,
            'current_temp' => $this->telemetry->cargo_temperature_celsius,
            'target_range' => [
                'min' => $this->delivery->target_temp_min_celsius,
                'max' => $this->delivery->target_temp_max_celsius,
            ],
            'severity' => 'critical',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
```

---

#### **6. Route Optimization Service**
**Status**: ❌ Missing  
**Current**: Direct line distance calculation  
**Required**: Real road/airspace routing with waypoints

**A) Install HTTP Client (already exists in Laravel)**

**B) Create Route Optimization Service**
```php
// app/Services/RouteOptimizationService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RouteOptimizationService
{
    /**
     * Get optimized route using OSRM (free, open-source)
     * Alternative: Use Mapbox Directions API (paid but better)
     */
    public function getOptimizedRoute($fromLat, $fromLng, $toLat, $toLng, array $options = []): array
    {
        $cacheKey = "route_{$fromLat}_{$fromLng}_{$toLat}_{$toLng}";
        
        return Cache::remember($cacheKey, 3600, function () use ($fromLat, $fromLng, $toLat, $toLng, $options) {
            
            // Option 1: OSRM (Free, Open Source)
            $response = Http::get('http://router.project-osrm.org/route/v1/driving/' . 
                "{$fromLng},{$fromLat};{$toLng},{$toLat}", [
                'overview' => 'full',
                'geometries' => 'geojson',
                'steps' => 'true',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Route optimization failed');
            }

            $data = $response->json();
            $route = $data['routes'][0] ?? null;

            if (!$route) {
                throw new \Exception('No route found');
            }

            // Extract waypoints
            $waypoints = [];
            foreach ($route['geometry']['coordinates'] as $coord) {
                $waypoints[] = [
                    'lng' => $coord[0],
                    'lat' => $coord[1],
                ];
            }

            return [
                'distance_meters' => $route['distance'],
                'distance_km' => round($route['distance'] / 1000, 2),
                'duration_seconds' => $route['duration'],
                'duration_minutes' => round($route['duration'] / 60, 2),
                'waypoints' => $waypoints,
                'geometry' => $route['geometry'],
            ];
        });
    }

    /**
     * Get optimized route using Mapbox (Premium)
     */
    public function getMapboxRoute($fromLat, $fromLng, $toLat, $toLng): array
    {
        $token = config('services.mapbox.access_token');
        
        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}", [
            'access_token' => $token,
            'geometries' => 'geojson',
            'overview' => 'full',
            'steps' => 'true',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Mapbox routing failed');
        }

        $data = $response->json();
        $route = $data['routes'][0] ?? null;

        if (!$route) {
            throw new \Exception('No route found');
        }

        $waypoints = [];
        foreach ($route['geometry']['coordinates'] as $coord) {
            $waypoints[] = [
                'lng' => $coord[0],
                'lat' => $coord[1],
            ];
        }

        return [
            'distance_meters' => $route['distance'],
            'distance_km' => round($route['distance'] / 1000, 2),
            'duration_seconds' => $route['duration'],
            'duration_minutes' => round($route['duration'] / 60, 2),
            'waypoints' => $waypoints,
            'geometry' => $route['geometry'],
        ];
    }

    /**
     * Calculate estimated flight time considering wind and altitude
     */
    public function calculateFlightTime($distanceKm, $droneSpeedKmh = 80, $windSpeedKmh = 15): int
    {
        // Adjust for headwind (simplified)
        $effectiveSpeed = $droneSpeedKmh - ($windSpeedKmh * 0.3);
        
        $timeHours = $distanceKm / $effectiveSpeed;
        $timeMinutes = $timeHours * 60;
        
        // Add 5 minutes for takeoff/landing
        return (int) ceil($timeMinutes + 5);
    }
}
```

**C) Update Delivery Creation to Use Route Optimization**  
File: `app/Http/Controllers/DeliveryController.php`

In `store()` method:
```php
use App\Services\RouteOptimizationService;

// Calculate optimized route
$routeService = new RouteOptimizationService();
$pickupCoords = json_decode($deliveryRequest->pickup_coordinates ?? '{}', true);
$deliveryCoords = json_decode($deliveryRequest->delivery_coordinates ?? '{}', true);

try {
    $route = $routeService->getOptimizedRoute(
        $pickupCoords['latitude'] ?? $hub->latitude,
        $pickupCoords['longitude'] ?? $hub->longitude,
        $deliveryCoords['latitude'] ?? $hospital->latitude,
        $deliveryCoords['longitude'] ?? $hospital->longitude
    );

    $validated['route_waypoints'] = json_encode($route['waypoints']);
    $validated['total_distance_km'] = $route['distance_km'];
    $validated['estimated_delivery_time'] = now()->addMinutes($route['duration_minutes']);
} catch (\Exception $e) {
    // Fallback to straight-line distance
    Log::warning('Route optimization failed: ' . $e->getMessage());
}
```

---

#### **7. Predictive Maintenance**
**Status**: ⚠️ Basic (has last_maintenance_date, next_maintenance_due)  
**Current**: Manual maintenance scheduling  
**Required**: Automatic maintenance alerts based on usage

**A) Create Maintenance Prediction Service**
```php
// app/Services/PredictiveMaintenanceService.php
<?php

namespace App\Services;

use App\Models\Drone;
use Carbon\Carbon;

class PredictiveMaintenanceService
{
    const MAINTENANCE_THRESHOLDS = [
        'flight_hours_critical' => 200,      // Every 200 hours
        'flight_hours_warning' => 180,       // Warning at 180 hours
        'delivery_count_critical' => 500,    // Every 500 deliveries
        'battery_cycles_critical' => 300,    // Every 300 charge cycles
        'days_since_maintenance' => 90,      // Every 90 days minimum
    ];

    /**
     * Check if drone needs maintenance
     */
    public function needsMaintenance(Drone $drone): array
    {
        $reasons = [];
        $priority = 'low';

        // Check flight hours
        $hoursSinceMaintenance = $drone->total_flight_hours - ($drone->last_maintenance_flight_hours ?? 0);
        if ($hoursSinceMaintenance >= self::MAINTENANCE_THRESHOLDS['flight_hours_critical']) {
            $reasons[] = "Flight hours exceeded ({$hoursSinceMaintenance}h)";
            $priority = 'critical';
        } elseif ($hoursSinceMaintenance >= self::MAINTENANCE_THRESHOLDS['flight_hours_warning']) {
            $reasons[] = "Approaching flight hours limit ({$hoursSinceMaintenance}h)";
            $priority = 'warning';
        }

        // Check delivery count
        $deliveriesSinceMaintenance = $drone->total_deliveries - ($drone->last_maintenance_deliveries ?? 0);
        if ($deliveriesSinceMaintenance >= self::MAINTENANCE_THRESHOLDS['delivery_count_critical']) {
            $reasons[] = "Delivery count exceeded ({$deliveriesSinceMaintenance} deliveries)";
            $priority = 'critical';
        }

        // Check time since last maintenance
        if ($drone->last_maintenance_date) {
            $daysSince = Carbon::parse($drone->last_maintenance_date)->diffInDays(now());
            if ($daysSince >= self::MAINTENANCE_THRESHOLDS['days_since_maintenance']) {
                $reasons[] = "Time since last maintenance ({$daysSince} days)";
                if ($priority === 'low') $priority = 'warning';
            }
        }

        // Check battery health
        if ($drone->battery_health_percentage && $drone->battery_health_percentage < 70) {
            $reasons[] = "Battery health degraded ({$drone->battery_health_percentage}%)";
            $priority = 'critical';
        }

        return [
            'needs_maintenance' => count($reasons) > 0,
            'priority' => $priority,
            'reasons' => $reasons,
            'recommended_date' => $this->calculateNextMaintenanceDate($drone),
        ];
    }

    /**
     * Calculate next maintenance date
     */
    protected function calculateNextMaintenanceDate(Drone $drone): Carbon
    {
        $lastMaintenance = Carbon::parse($drone->last_maintenance_date ?? now());
        
        // Based on whichever comes first
        $dates = [
            $lastMaintenance->copy()->addDays(self::MAINTENANCE_THRESHOLDS['days_since_maintenance']),
            // Could add more predictive factors here
        ];

        return collect($dates)->min();
    }

    /**
     * Schedule maintenance for drone
     */
    public function scheduleMaintenance(Drone $drone, Carbon $scheduledDate): void
    {
        $drone->update([
            'next_maintenance_due' => $scheduledDate,
            'status' => 'maintenance',
        ]);

        // Notify maintenance team
        event(new \App\Events\MaintenanceScheduled($drone, $scheduledDate));
    }
}
```

**B) Create Artisan Command for Maintenance Check**
```bash
php artisan make:command CheckDroneMaintenance
```

```php
// app/Console/Commands/CheckDroneMaintenance.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Drone;
use App\Services\PredictiveMaintenanceService;

class CheckDroneMaintenance extends Command
{
    protected $signature = 'drones:check-maintenance';
    protected $description = 'Check all drones for predictive maintenance needs';

    public function handle()
    {
        $service = new PredictiveMaintenanceService();
        $drones = Drone::active()->get();

        $needsMaintenance = 0;

        foreach ($drones as $drone) {
            $check = $service->needsMaintenance($drone);

            if ($check['needs_maintenance']) {
                $this->warn("Drone {$drone->name} needs maintenance:");
                foreach ($check['reasons'] as $reason) {
                    $this->line("  - {$reason}");
                }

                if ($check['priority'] === 'critical') {
                    $drone->update(['status' => 'maintenance']);
                    $this->error("  → Status changed to MAINTENANCE");
                }

                $needsMaintenance++;
            }
        }

        $this->info("Total drones needing maintenance: {$needsMaintenance}");
    }
}
```

**C) Schedule Daily Maintenance Check**  
File: `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Check drone maintenance needs daily at 6 AM
    $schedule->command('drones:check-maintenance')->dailyAt('06:00');
}
```

---

### **🟢 MEDIUM PRIORITY (Nice to Have - Week 3-4)**

#### **8. Notification System (SMS/Email/Push)**
**Status**: ⚠️ Database structure exists, not implemented  
**Required**: Integration with Bangladeshi SMS providers and Email

**Implementation Guide:**
1. **SMS**: Use SSL Wireless, Bulk SMS BD, or Twilio
2. **Email**: Use SMTP (Mailgun/SendGrid) or local provider
3. **Push**: Firebase Cloud Messaging (FCM)

**Detailed implementation available in separate document**

---

#### **9. Real-Time Tracking (Maps + WebSockets)**
**Status**: ⚠️ API endpoints exist, no live broadcasting  
**Required**: Pusher/Laravel Echo integration with Mapbox

**This was covered in your previous Option A request**

---

#### **10. Analytics Dashboard**
**Status**: ✅ Basic charts exist  
**Enhancement**: Add more KPIs specific to Bangladesh operations

**Add to Admin Dashboard:**
- Division-wise delivery heatmap
- Average delivery time by region
- Cold-chain compliance rate
- Emergency delivery response time
- Hub utilization rates

---

## 📊 **Implementation Timeline**

| Week | Tasks | Priority |
|------|-------|----------|
| **Week 1** | Bangladesh location validation, Hubs system, Emergency priority | 🔴 Critical |
| **Week 2** | Delivery proof (OTP/Photo), Cold-chain monitoring | 🔴 Critical |
| **Week 3** | Route optimization, Predictive maintenance | 🟡 High |
| **Week 4** | SMS notifications, Real-time tracking | 🟡 High |
| **Week 5** | Analytics enhancements, Testing | 🟢 Medium |

---

## 🔧 **Quick Start Commands**

```bash
# Create all required migrations
php artisan make:migration create_hubs_table
php artisan make:migration create_hub_inventories_table
php artisan make:migration add_delivery_proof_to_deliveries_table
php artisan make:migration add_cold_chain_to_deliveries

# Create models
php artisan make:model Hub
php artisan make:model HubInventory

# Create seeders
php artisan make:seeder HubSeeder
php artisan make:seeder BangladeshLocationsSeeder

# Create services
mkdir app/Services
# Create files as shown above

# Create commands
php artisan make:command AutoAssignDeliveries
php artisan make:command CheckDroneMaintenance

# Run migrations and seed
php artisan migrate
php artisan db:seed --class=HubSeeder
```

---

## 📝 **Configuration Checklist**

- [ ] Add `MAPBOX_ACCESS_TOKEN` to `.env`
- [ ] Add `SMS_PROVIDER_KEY` to `.env` (SSL Wireless/BulkSMS BD)
- [ ] Add `PUSHER_APP_KEY` for real-time tracking
- [ ] Configure `MAIL_*` settings for email notifications
- [ ] Set up scheduled tasks in `app/Console/Kernel.php`
- [ ] Configure file storage for delivery proof photos
- [ ] Set up backup strategy for database
- [ ] Configure Redis for queue jobs and caching

---

## 🚀 **Next Steps**

1. **Review this plan** and confirm priority order
2. **I can implement any section immediately** - just tell me which one to start with
3. **Test in staging** before production deployment
4. **Get Mapbox token** (free tier: 100,000 requests/month)
5. **Choose SMS provider** for Bangladesh (SSL Wireless recommended)

**Which modification should I implement first?** 
- A) Bangladesh location validation + Hubs
- B) Delivery proof system (OTP + Photo)
- C) Emergency priority queue
- D) Cold-chain monitoring
- E) All of the above (full implementation)

Let me know and I'll start coding immediately! 🚀
