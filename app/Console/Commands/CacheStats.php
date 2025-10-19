<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display cache configuration and statistics';

    protected CacheService $cacheService;

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService): int
    {
        $this->cacheService = $cacheService;

        $this->info('Cache Configuration & Statistics');
        $this->info('================================');
        $this->newLine();

        // Cache configuration
        $this->line('<fg=cyan>Configuration:</>');
        $stats = $this->cacheService->getStats();
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['Cache Driver', $stats['driver']],
                ['TTL Short (Frequent changes)', $stats['ttl_short']],
                ['TTL Medium (Moderate changes)', $stats['ttl_medium']],
                ['TTL Long (Stable data)', $stats['ttl_long']],
                ['TTL Very Long (Rarely changes)', $stats['ttl_very_long']],
            ]
        );

        $this->newLine();

        // Cache types
        $this->line('<fg=cyan>Cached Data Types:</>');
        $this->table(
            ['Type', 'Example Keys', 'TTL'],
            [
                ['Dashboard Stats', 'stats:dashboard', 'Short (5 min)'],
                ['Delivery Tracking', 'delivery:tracking:{number}', 'Short (5 min)'],
                ['Drone Status', 'drone:status:{id}', 'Short (5 min)'],
                ['Available Drones', 'drone:available', 'Short (5 min)'],
                ['Hospital Details', 'hospital:{id}', 'Medium (30 min)'],
                ['Active Hospitals', 'hospital:active', 'Medium (30 min)'],
                ['Low Stock Supplies', 'supply:low_stock', 'Medium (30 min)'],
                ['Delivery Stats', 'stats:delivery:{date}', 'Long (1 hour)'],
            ]
        );

        $this->newLine();

        // Recommendations
        $this->line('<fg=cyan>Recommendations:</>');
        
        $driver = config('cache.default');
        
        if ($driver === 'file' || $driver === 'database') {
            $this->warn('⚠️  Using ' . strtoupper($driver) . ' cache driver.');
            $this->line('   For better performance in production, consider:');
            $this->line('   - Redis (recommended)');
            $this->line('   - Memcached');
            $this->newLine();
        } else {
            $this->info('✅ Using ' . strtoupper($driver) . ' cache driver (good for production)');
            $this->newLine();
        }

        // Common commands
        $this->line('<fg=cyan>Common Commands:</>');
        $this->line('• php artisan cache:warm      - Warm up cache with frequently accessed data');
        $this->line('• php artisan cache:clear     - Clear all application cache');
        $this->line('• php artisan config:cache    - Cache configuration files');
        $this->line('• php artisan route:cache     - Cache route definitions');
        $this->line('• php artisan view:cache      - Cache compiled Blade views');

        $this->newLine();

        return Command::SUCCESS;
    }
}
