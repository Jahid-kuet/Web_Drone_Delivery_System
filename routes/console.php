<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic delivery assignment every 5 minutes
Schedule::command('deliveries:auto-assign')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Auto-assignment completed successfully');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Auto-assignment failed');
    });

// Check for emergency alerts every minute
Schedule::command('deliveries:auto-assign --check-alerts')
    ->everyMinute()
    ->withoutOverlapping();
