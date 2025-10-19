<?php

namespace App\Observers;

use App\Models\Drone;
use App\Services\CacheService;

class DroneObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Drone "created" event.
     */
    public function created(Drone $drone): void
    {
        $this->cacheService->invalidateDroneCache($drone);
    }

    /**
     * Handle the Drone "updated" event.
     */
    public function updated(Drone $drone): void
    {
        $this->cacheService->invalidateDroneCache($drone);
    }

    /**
     * Handle the Drone "deleted" event.
     */
    public function deleted(Drone $drone): void
    {
        $this->cacheService->invalidateDroneCache($drone);
    }
}
