<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasGPSCoordinates
 * 
 * Provides GPS/location functionality for models with latitude/longitude fields
 */
trait HasGPSCoordinates
{
    /**
     * Get the latitude field name
     */
    protected function getLatitudeField(): string
    {
        return 'latitude';
    }

    /**
     * Get the longitude field name
     */
    protected function getLongitudeField(): string
    {
        return 'longitude';
    }

    /**
     * Update GPS coordinates
     */
    public function updateCoordinates(float $latitude, float $longitude): bool
    {
        if (!$this->isValidCoordinates($latitude, $longitude)) {
            throw new \InvalidArgumentException('Invalid GPS coordinates');
        }

        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        $this->{$latField} = $latitude;
        $this->{$lngField} = $longitude;

        return $this->save();
    }

    /**
     * Validate GPS coordinates
     */
    public function isValidCoordinates(float $latitude, float $longitude): bool
    {
        return $latitude >= -90 && $latitude <= 90 
            && $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Check if model has coordinates set
     */
    public function hasCoordinates(): bool
    {
        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        return !is_null($this->{$latField}) && !is_null($this->{$lngField});
    }

    /**
     * Calculate distance to another location (in kilometers)
     * Uses Haversine formula
     */
    public function distanceTo(float|self $latitude, ?float $longitude = null): float
    {
        // Handle object or coordinates
        if ($latitude instanceof self) {
            $latField = $this->getLatitudeField();
            $lngField = $this->getLongitudeField();
            $targetLat = $latitude->{$latField};
            $targetLng = $latitude->{$lngField};
        } else {
            $targetLat = $latitude;
            $targetLng = $longitude;
        }

        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        $lat1 = deg2rad($this->{$latField});
        $lon1 = deg2rad($this->{$lngField});
        $lat2 = deg2rad($targetLat);
        $lon2 = deg2rad($targetLng);

        $earthRadius = 6371; // kilometers

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Get formatted coordinates string
     */
    public function getFormattedCoordinatesAttribute(): string
    {
        if (!$this->hasCoordinates()) {
            return 'N/A';
        }

        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        $lat = $this->{$latField};
        $lng = $this->{$lngField};

        $latDir = $lat >= 0 ? 'N' : 'S';
        $lngDir = $lng >= 0 ? 'E' : 'W';

        return sprintf(
            '%s° %s, %s° %s',
            abs(round($lat, 6)),
            $latDir,
            abs(round($lng, 6)),
            $lngDir
        );
    }

    /**
     * Get Google Maps URL
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        if (!$this->hasCoordinates()) {
            return '#';
        }

        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        return sprintf(
            'https://www.google.com/maps?q=%s,%s',
            $this->{$latField},
            $this->{$lngField}
        );
    }

    /**
     * Scope: Within radius (in kilometers)
     */
    public function scopeWithinRadius(Builder $query, float $latitude, float $longitude, float $radius): Builder
    {
        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        // Haversine formula for SQL
        $haversine = sprintf(
            '(6371 * acos(cos(radians(?)) * cos(radians(%s)) * cos(radians(%s) - radians(?)) + sin(radians(?)) * sin(radians(%s))))',
            $latField,
            $lngField,
            $latField
        );

        return $query->whereRaw("{$haversine} <= ?", [
            $latitude,
            $longitude,
            $latitude,
            $radius,
        ]);
    }

    /**
     * Scope: Order by distance from a point
     */
    public function scopeOrderByDistance(Builder $query, float $latitude, float $longitude, string $direction = 'asc'): Builder
    {
        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        $haversine = sprintf(
            '(6371 * acos(cos(radians(?)) * cos(radians(%s)) * cos(radians(%s) - radians(?)) + sin(radians(?)) * sin(radians(%s))))',
            $latField,
            $lngField,
            $latField
        );

        return $query->orderByRaw("{$haversine} {$direction}", [
            $latitude,
            $longitude,
            $latitude,
        ]);
    }

    /**
     * Scope: Find nearest locations
     */
    public function scopeNearest(Builder $query, float $latitude, float $longitude, int $limit = 10): Builder
    {
        return $query->orderByDistance($latitude, $longitude)->limit($limit);
    }

    /**
     * Get bearing (direction) to another location in degrees (0-360)
     */
    public function bearingTo(float|self $latitude, ?float $longitude = null): float
    {
        // Handle object or coordinates
        if ($latitude instanceof self) {
            $latField = $this->getLatitudeField();
            $lngField = $this->getLongitudeField();
            $targetLat = $latitude->{$latField};
            $targetLng = $latitude->{$lngField};
        } else {
            $targetLat = $latitude;
            $targetLng = $longitude;
        }

        $latField = $this->getLatitudeField();
        $lngField = $this->getLongitudeField();

        $lat1 = deg2rad($this->{$latField});
        $lon1 = deg2rad($this->{$lngField});
        $lat2 = deg2rad($targetLat);
        $lon2 = deg2rad($targetLng);

        $dLon = $lon2 - $lon1;

        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($dLon);

        $bearing = rad2deg(atan2($y, $x));

        return ($bearing + 360) % 360;
    }

    /**
     * Get compass direction (N, NE, E, SE, S, SW, W, NW)
     */
    public function compassDirectionTo(float|self $latitude, ?float $longitude = null): string
    {
        $bearing = $this->bearingTo($latitude, $longitude);

        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
        $index = round($bearing / 45) % 8;

        return $directions[$index];
    }
}
