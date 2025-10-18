<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limiter (for web routes with API calls)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Public API - Limited by IP address only
        RateLimiter::for('public-api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please try again later.',
                        'error' => 'rate_limit_exceeded',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // Authenticated API - Read operations (higher limit)
        RateLimiter::for('api-read', function (Request $request) {
            return Limit::perMinute(100)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please slow down.',
                        'error' => 'rate_limit_exceeded',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // Authenticated API - Write operations (stricter limit)
        RateLimiter::for('api-write', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many write requests. Please wait before trying again.',
                        'error' => 'rate_limit_exceeded',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // Real-time tracking - Higher limit for frequent updates
        RateLimiter::for('realtime', function (Request $request) {
            return Limit::perMinute(120)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many tracking requests. Please reduce polling frequency.',
                        'error' => 'rate_limit_exceeded',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // OTP generation - Very strict limit to prevent abuse
        RateLimiter::for('otp-generation', function (Request $request) {
            return [
                // 5 OTP requests per minute
                Limit::perMinute(5)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function () {
                        return response()->json([
                            'success' => false,
                            'message' => 'Too many OTP generation attempts. Please wait before requesting a new OTP.',
                            'error' => 'otp_rate_limit_exceeded',
                            'retry_after' => 60,
                        ], 429);
                    }),
                // 20 OTP requests per hour (additional layer)
                Limit::perHour(20)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function () {
                        return response()->json([
                            'success' => false,
                            'message' => 'Daily OTP limit reached. Please contact support if you need assistance.',
                            'error' => 'otp_hourly_limit_exceeded',
                            'retry_after' => 3600,
                        ], 429);
                    }),
            ];
        });

        // Photo uploads - Moderate limit
        RateLimiter::for('file-upload', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many file uploads. Please wait before uploading again.',
                        'error' => 'upload_rate_limit_exceeded',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // Health check - High limit for monitoring tools
        RateLimiter::for('health-check', function (Request $request) {
            return Limit::perMinute(180)->by($request->ip());
        });

        // Authentication attempts - Very strict
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Global API limit (fallback)
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });
    }
}
