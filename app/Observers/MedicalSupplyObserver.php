<?php

namespace App\Observers;

use App\Models\MedicalSupply;
use App\Services\CacheService;

class MedicalSupplyObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the MedicalSupply "created" event.
     */
    public function created(MedicalSupply $supply): void
    {
        $this->cacheService->invalidateSupplyCache($supply);
    }

    /**
     * Handle the MedicalSupply "updated" event.
     */
    public function updated(MedicalSupply $supply): void
    {
        $this->cacheService->invalidateSupplyCache($supply);
    }

    /**
     * Handle the MedicalSupply "deleted" event.
     */
    public function deleted(MedicalSupply $supply): void
    {
        $this->cacheService->invalidateSupplyCache($supply);
    }
}
