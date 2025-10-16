<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BangladeshLocationService
{
    // Bangladesh bounding box (covers entire country)
    const BANGLADESH_BOUNDS = [
        'min_lat' => 20.670883,
        'max_lat' => 26.631945,
        'min_lng' => 88.028336,
        'max_lng' => 92.673668,
    ];

    // Khulna Division bounding box (tighter validation for initial launch)
    const KHULNA_BOUNDS = [
        'min_lat' => 21.5,
        'max_lat' => 23.5,
        'min_lng' => 88.5,
        'max_lng' => 90.5,
    ];

    /**
     * Check if coordinates are within Bangladesh
     */
    public static function isInBangladesh(float $lat, float $lng): bool
    {
        return $lat >= self::BANGLADESH_BOUNDS['min_lat'] && 
               $lat <= self::BANGLADESH_BOUNDS['max_lat'] &&
               $lng >= self::BANGLADESH_BOUNDS['min_lng'] && 
               $lng <= self::BANGLADESH_BOUNDS['max_lng'];
    }

    /**
     * Check if coordinates are within Khulna Division
     */
    public static function isInKhulna(float $lat, float $lng): bool
    {
        return $lat >= self::KHULNA_BOUNDS['min_lat'] && 
               $lat <= self::KHULNA_BOUNDS['max_lat'] &&
               $lng >= self::KHULNA_BOUNDS['min_lng'] && 
               $lng <= self::KHULNA_BOUNDS['max_lng'];
    }

    /**
     * Validate location and return details
     * For Khulna launch: enforce Khulna division only
     */
    public static function validateLocation(float $lat, float $lng, bool $strictKhulna = false): array
    {
        // First check if in Bangladesh
        if (!self::isInBangladesh($lat, $lng)) {
            return [
                'valid' => false,
                'error' => 'Location must be within Bangladesh',
                'latitude' => $lat,
                'longitude' => $lng,
            ];
        }

        // For Khulna launch, enforce Khulna division
        if ($strictKhulna && !self::isInKhulna($lat, $lng)) {
            return [
                'valid' => false,
                'error' => 'Currently, service is only available in Khulna Division',
                'suggestion' => 'Please select a location within Khulna',
                'latitude' => $lat,
                'longitude' => $lng,
            ];
        }

        // Optional: Use Mapbox reverse geocoding for detailed validation
        $reverseGeocode = self::reverseGeocode($lat, $lng);

        return [
            'valid' => true,
            'latitude' => $lat,
            'longitude' => $lng,
            'division' => $reverseGeocode['division'] ?? 'Unknown',
            'district' => $reverseGeocode['district'] ?? 'Unknown',
            'in_khulna' => self::isInKhulna($lat, $lng),
        ];
    }

    /**
     * Reverse geocode coordinates to get location details
     */
    protected static function reverseGeocode(float $lat, float $lng): array
    {
        try {
            $token = config('services.mapbox.access_token');
            
            if (!$token) {
                return ['division' => 'Unknown', 'district' => 'Unknown'];
            }

            $response = Http::timeout(5)->get("https://api.mapbox.com/geocoding/v5/mapbox.places/{$lng},{$lat}.json", [
                'access_token' => $token,
                'types' => 'place,district,region',
                'language' => 'en',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $features = $data['features'] ?? [];
                
                $division = null;
                $district = null;

                foreach ($features as $feature) {
                    $placeType = $feature['place_type'][0] ?? '';
                    $placeName = $feature['text'] ?? '';

                    if ($placeType === 'region') {
                        $division = $placeName;
                    } elseif ($placeType === 'district' || $placeType === 'place') {
                        $district = $placeName;
                    }
                }

                return [
                    'division' => $division ?? 'Unknown',
                    'district' => $district ?? 'Unknown',
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('Reverse geocoding failed: ' . $e->getMessage());
        }

        return ['division' => 'Unknown', 'district' => 'Unknown'];
    }

    /**
     * Get Bangladesh divisions with coordinates (major cities)
     */
    public static function getDivisions(): array
    {
        return [
            'dhaka' => [
                'lat' => 23.8103,
                'lng' => 90.4125,
                'name' => 'Dhaka',
                'name_bn' => 'ঢাকা',
            ],
            'chittagong' => [
                'lat' => 22.3569,
                'lng' => 91.7832,
                'name' => 'Chittagong',
                'name_bn' => 'চট্টগ্রাম',
            ],
            'rajshahi' => [
                'lat' => 24.3745,
                'lng' => 88.6042,
                'name' => 'Rajshahi',
                'name_bn' => 'রাজশাহী',
            ],
            'khulna' => [
                'lat' => 22.8456,
                'lng' => 89.5403,
                'name' => 'Khulna',
                'name_bn' => 'খুলনা',
                'priority' => 1, // Primary launch city
            ],
            'barisal' => [
                'lat' => 22.7010,
                'lng' => 90.3535,
                'name' => 'Barisal',
                'name_bn' => 'বরিশাল',
            ],
            'sylhet' => [
                'lat' => 24.8949,
                'lng' => 91.8687,
                'name' => 'Sylhet',
                'name_bn' => 'সিলেট',
            ],
            'rangpur' => [
                'lat' => 25.7439,
                'lng' => 89.2752,
                'name' => 'Rangpur',
                'name_bn' => 'রংপুর',
            ],
            'mymensingh' => [
                'lat' => 24.7471,
                'lng' => 90.4203,
                'name' => 'Mymensingh',
                'name_bn' => 'ময়মনসিংহ',
            ],
        ];
    }

    /**
     * Get Khulna district hospitals (sample locations)
     */
    public static function getKhulnaHospitals(): array
    {
        return [
            [
                'name' => 'Khulna Medical College Hospital',
                'lat' => 22.8148,
                'lng' => 89.5680,
                'type' => 'medical_college',
                'district' => 'Khulna',
            ],
            [
                'name' => 'Khulna City Medical College Hospital',
                'lat' => 22.8350,
                'lng' => 89.5400,
                'type' => 'medical_college',
                'district' => 'Khulna',
            ],
            [
                'name' => 'Khulna Diabetic Hospital',
                'lat' => 22.8300,
                'lng' => 89.5500,
                'type' => 'specialized',
                'district' => 'Khulna',
            ],
            [
                'name' => 'Gazi Medical College Hospital',
                'lat' => 22.8200,
                'lng' => 89.5600,
                'type' => 'medical_college',
                'district' => 'Khulna',
            ],
            [
                'name' => 'Jessore General Hospital',
                'lat' => 23.1634,
                'lng' => 89.2182,
                'type' => 'general',
                'district' => 'Jessore',
            ],
            [
                'name' => 'Satkhira Sadar Hospital',
                'lat' => 22.7185,
                'lng' => 89.0700,
                'type' => 'district',
                'district' => 'Satkhira',
            ],
        ];
    }

    /**
     * Calculate distance between two points in kilometers
     */
    public static function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Find nearest division from coordinates
     */
    public static function findNearestDivision(float $lat, float $lng): ?string
    {
        $divisions = self::getDivisions();
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($divisions as $key => $division) {
            $distance = self::calculateDistance($lat, $lng, $division['lat'], $division['lng']);
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $key;
            }
        }

        return $nearest;
    }

    /**
     * Check if location is in operational area (Khulna for now)
     */
    public static function isInOperationalArea(float $lat, float $lng): bool
    {
        // For initial launch, only Khulna division is operational
        return self::isInKhulna($lat, $lng);
    }

    /**
     * Get operational area message
     */
    public static function getOperationalAreaMessage(): string
    {
        return 'Currently, our drone delivery service is available in Khulna Division only. '
             . 'We are expanding to other divisions soon!';
    }
}
