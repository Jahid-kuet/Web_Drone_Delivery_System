<?php

namespace App\Observers;

use App\Models\Hospital;
use App\Services\CacheService;

class HospitalObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Hospital "created" event.
     */
    public function created(Hospital $hospital): void
    {
        $this->cacheService->invalidateHospitalCache($hospital);
    }

    /**
     * Handle the Hospital "updated" event.
     */
    public function updated(Hospital $hospital): void
    {
        $this->cacheService->invalidateHospitalCache($hospital);
    }

    /**
     * Handle the Hospital "deleted" event.
     */
    public function deleted(Hospital $hospital): void
    {
        $this->cacheService->invalidateHospitalCache($hospital);
    }
}
