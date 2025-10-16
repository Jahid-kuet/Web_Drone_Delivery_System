<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubInventory extends Model
{
    protected $fillable = [
        'hub_id',
        'medical_supply_id',
        'quantity_available',
        'minimum_stock_level',
        'maximum_stock_level',
        'reorder_quantity',
        'reorder_point',
        'needs_cold_storage',
        'storage_temperature_celsius',
        'last_restocked_date',
        'last_restock_quantity',
        'notes',
    ];

    protected $casts = [
        'needs_cold_storage' => 'boolean',
        'storage_temperature_celsius' => 'decimal:2',
        'last_restocked_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function medicalSupply()
    {
        return $this->belongsTo(MedicalSupply::class);
    }

    /**
     * Scopes
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_available', '<=', 'minimum_stock_level');
    }

    public function scopeNeedsReorder($query)
    {
        return $query->whereColumn('quantity_available', '<=', 'reorder_point');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_available', 0);
    }

    public function scopeRequiresColdStorage($query)
    {
        return $query->where('needs_cold_storage', true);
    }

    /**
     * Accessors
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantity_available <= 0) {
            return 'out_of_stock';
        } elseif ($this->quantity_available <= $this->minimum_stock_level) {
            return 'low_stock';
        } elseif ($this->quantity_available <= $this->reorder_point) {
            return 'reorder_needed';
        } elseif ($this->quantity_available >= $this->maximum_stock_level) {
            return 'overstocked';
        }

        return 'adequate';
    }

    public function getStockPercentageAttribute(): int
    {
        if ($this->maximum_stock_level <= 0) {
            return 0;
        }

        return (int) min(100, ($this->quantity_available / $this->maximum_stock_level) * 100);
    }

    /**
     * Methods
     */
    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->minimum_stock_level;
    }

    public function needsReorder(): bool
    {
        return $this->quantity_available <= $this->reorder_point;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity_available <= 0;
    }

    public function canFulfill(int $quantity): bool
    {
        return $this->quantity_available >= $quantity;
    }

    /**
     * Decrease inventory (for delivery)
     */
    public function decreaseStock(int $quantity, string $reason = null): bool
    {
        if (!$this->canFulfill($quantity)) {
            return false;
        }

        $this->quantity_available -= $quantity;
        $this->save();

        // Log the transaction
        \Log::info("Hub inventory decreased", [
            'hub_id' => $this->hub_id,
            'supply_id' => $this->medical_supply_id,
            'quantity' => $quantity,
            'remaining' => $this->quantity_available,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Increase inventory (restock)
     */
    public function restock(int $quantity): bool
    {
        $this->quantity_available += $quantity;
        $this->last_restocked_date = now();
        $this->last_restock_quantity = $quantity;
        $this->save();

        \Log::info("Hub inventory restocked", [
            'hub_id' => $this->hub_id,
            'supply_id' => $this->medical_supply_id,
            'quantity' => $quantity,
            'new_total' => $this->quantity_available,
        ]);

        return true;
    }

    /**
     * Auto-reorder if needed
     */
    public function checkAndCreateReorderAlert(): ?array
    {
        if ($this->needsReorder()) {
            return [
                'hub' => $this->hub->name,
                'supply' => $this->medicalSupply->name,
                'current_quantity' => $this->quantity_available,
                'reorder_quantity' => $this->reorder_quantity,
                'urgency' => $this->isOutOfStock() ? 'critical' : 'high',
            ];
        }

        return null;
    }
}
