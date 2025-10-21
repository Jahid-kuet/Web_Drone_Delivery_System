<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class CacheWarmUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up application cache with frequently accessed data';

    protected CacheService $cacheService;

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService): int
    {
        $this->cacheService = $cacheService;

        $this->info('Warming up application cache...');
        $this->newLine();

        $result = $this->cacheService->warmUp();

        if ($result['success']) {
            $this->info('[OK] Cache warmed up successfully!');
            $this->newLine();
            
            $this->table(
                ['Cache Key', 'Status'],
                array_map(fn($key) => [$key, '[OK] Cached'], $result['warmed'])
            );

            $this->newLine();
            $this->info("Total cached items: {$result['count']}");
            
            return Command::SUCCESS;
        } else {
            $this->error('❌ Failed to warm up cache');
            $this->error('Error: ' . $result['error']);
            
            if (!empty($result['warmed'])) {
                $this->warn('Partially warmed: ' . implode(', ', $result['warmed']));
            }
            
            return Command::FAILURE;
        }
    }
}
