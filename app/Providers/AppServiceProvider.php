<?php

namespace App\Providers;

use App\Models\Delivery;
use App\Models\Drone;
use App\Models\Hospital;
use App\Models\MedicalSupply;
use App\Observers\DeliveryObserver;
use App\Observers\DroneObserver;
use App\Observers\HospitalObserver;
use App\Observers\MedicalSupplyObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers for automatic cache invalidation and SMS notifications
        Delivery::observe(DeliveryObserver::class);
        Drone::observe(DroneObserver::class);
        Hospital::observe(HospitalObserver::class);
        MedicalSupply::observe(MedicalSupplyObserver::class);
    }
}
