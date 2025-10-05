<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class MedicalSupply extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'type',
        'weight_kg',
        'volume_ml',
        'dimensions',
        'quantity_available',
        'minimum_stock_level',
        'unit_price',
        'manufacturer',
        'batch_number',
        'expiry_date',
        'storage_requirements',
        'handling_instructions',
        'requires_cold_chain',
        'temperature_min',
        'temperature_max',
        'is_hazardous',
        'is_controlled_substance',
        'priority_level',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'dimensions' => 'array',
        'storage_requirements' => 'array',
        'handling_instructions' => 'array',
        'metadata' => 'array',
        'expiry_date' => 'date',
        'weight_kg' => 'decimal:3',
        'volume_ml' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'temperature_min' => 'decimal:2',
        'temperature_max' => 'decimal:2',
        'requires_cold_chain' => 'boolean',
        'is_hazardous' => 'boolean',
        'is_controlled_substance' => 'boolean',
        'is_active' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    // Constants for categories
    const CATEGORY_BLOOD_PRODUCTS = 'blood_products';
    const CATEGORY_MEDICINES = 'medicines';
    const CATEGORY_VACCINES = 'vaccines';
    const CATEGORY_SURGICAL_INSTRUMENTS = 'surgical_instruments';
    const CATEGORY_EMERGENCY_SUPPLIES = 'emergency_supplies';
    const CATEGORY_DIAGNOSTIC_KITS = 'diagnostic_kits';
    const CATEGORY_MEDICAL_DEVICES = 'medical_devices';

    // Constants for types
    const TYPE_LIQUID = 'liquid';
    const TYPE_SOLID = 'solid';
    const TYPE_FRAGILE = 'fragile';
    const TYPE_TEMPERATURE_SENSITIVE = 'temperature_sensitive';

    // Constants for priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    /**
     * Scope for active supplies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for supplies low in stock
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_available', '<=', 'minimum_stock_level');
    }

    /**
     * Scope for expired supplies
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope for expiring soon (within 30 days)
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    /**
     * Scope for supplies by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for supplies requiring cold chain
     */
    public function scopeRequiresColdChain($query)
    {
        return $query->where('requires_cold_chain', true);
    }

    /**
     * Scope for hazardous supplies
     */
    public function scopeHazardous($query)
    {
        return $query->where('is_hazardous', true);
    }

    /**
     * Check if supply is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if supply is expiring soon
     */
    public function isExpiringSoon($days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isBetween(now(), now()->addDays($days));
    }

    /**
     * Check if supply is low in stock
     */
    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->minimum_stock_level;
    }

    /**
     * Check if supply is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity_available <= 0;
    }

    /**
     * Check if supply is available for delivery
     */
    public function isAvailableForDelivery($quantity = 1): bool
    {
        return $this->is_active && 
               !$this->isExpired() && 
               $this->quantity_available >= $quantity;
    }

    /**
     * Get formatted weight
     */
    public function getFormattedWeightAttribute(): string
    {
        return $this->weight_kg . ' kg';
    }

    /**
     * Get formatted volume
     */
    public function getFormattedVolumeAttribute(): string
    {
        return $this->volume_ml ? $this->volume_ml . ' ml' : 'N/A';
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) return 'Out of Stock';
        if ($this->isLowStock()) return 'Low Stock';
        return 'In Stock';
    }

    /**
     * Get expiry status
     */
    public function getExpiryStatusAttribute(): string
    {
        if ($this->isExpired()) return 'Expired';
        if ($this->isExpiringSoon()) return 'Expiring Soon';
        return 'Valid';
    }

    /**
     * Reduce stock quantity
     */
    public function reduceStock(int $quantity): bool
    {
        if ($this->quantity_available >= $quantity) {
            $this->decrement('quantity_available', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increase stock quantity
     */
    public function increaseStock(int $quantity): bool
    {
        $this->increment('quantity_available', $quantity);
        return true;
    }

    /**
     * Get categories array
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_BLOOD_PRODUCTS => 'Blood Products',
            self::CATEGORY_MEDICINES => 'Medicines',
            self::CATEGORY_VACCINES => 'Vaccines',
            self::CATEGORY_SURGICAL_INSTRUMENTS => 'Surgical Instruments',
            self::CATEGORY_EMERGENCY_SUPPLIES => 'Emergency Supplies',
            self::CATEGORY_DIAGNOSTIC_KITS => 'Diagnostic Kits',
            self::CATEGORY_MEDICAL_DEVICES => 'Medical Devices'
        ];
    }

    /**
     * Get types array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_LIQUID => 'Liquid',
            self::TYPE_SOLID => 'Solid',
            self::TYPE_FRAGILE => 'Fragile',
            self::TYPE_TEMPERATURE_SENSITIVE => 'Temperature Sensitive'
        ];
    }

    /**
     * Get priority levels array
     */
    public static function getPriorityLevels(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_CRITICAL => 'Critical'
        ];
    }
}
