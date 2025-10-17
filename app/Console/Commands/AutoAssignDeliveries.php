<?php

namespace App\Console\Commands;

use App\Services\DeliveryPriorityQueue;
use Illuminate\Console\Command;

class AutoAssignDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deliveries:auto-assign {--check-alerts : Check for emergency alerts only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically assign pending deliveries to available drones based on priority';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚁 Starting automatic delivery assignment...');
        $this->newLine();

        // Check for emergency alerts
        $alerts = DeliveryPriorityQueue::checkEmergencyAlerts();
        
        if (!empty($alerts)) {
            $this->error('⚠️  EMERGENCY ALERTS:');
            foreach ($alerts as $alert) {
                $this->error("  • {$alert['message']}");
            }
            $this->newLine();
        }

        // If only checking alerts, stop here
        if ($this->option('check-alerts')) {
            return 0;
        }

        // Show current queue status
        $status = DeliveryPriorityQueue::getQueueStatus();
        $this->info("📊 Queue Status:");
        $this->line("  • Total Pending: {$status['total_pending']}");
        $this->line("  • Emergency: {$status['by_priority']['emergency']}");
        $this->line("  • Critical: {$status['by_priority']['critical']}");
        $this->line("  • High: {$status['by_priority']['high']}");
        $this->line("  • Medium: {$status['by_priority']['medium']}");
        $this->line("  • Low: {$status['by_priority']['low']}");
        $this->line("  • Available Drones: {$status['available_drones']}");
        $this->line("  • Oldest Wait: {$status['oldest_wait_minutes']} minutes");
        $this->newLine();

        // Perform auto-assignment
        if ($status['total_pending'] > 0) {
            $this->info('🔄 Processing assignments...');
            
            $results = DeliveryPriorityQueue::autoAssignDeliveries();
            
            $this->newLine();
            $this->info("✅ Assignment Results:");
            $this->line("  • Assigned: {$results['assigned']}");
            $this->line("  • Failed: {$results['failed']}");
            $this->line("  • Skipped: {$results['skipped']}");
            
            // Show details
            if ($results['assigned'] > 0) {
                $this->newLine();
                $this->info('📝 Assignment Details:');
                
                $table = [];
                foreach ($results['details'] as $detail) {
                    if ($detail['status'] === 'assigned') {
                        $priority = strtoupper($detail['priority_level'] ?? 'normal');
                        $score = $detail['priority_score'] ?? 0;
                        $table[] = [
                            "Delivery #{$detail['delivery_id']}",
                            "Drone #{$detail['drone_id']}",
                            $priority,
                            $score,
                        ];
                    }
                }
                
                $this->table(
                    ['Delivery', 'Drone', 'Priority', 'Score'],
                    $table
                );
            }
            
            if ($results['failed'] > 0) {
                $this->newLine();
                $this->warn('⚠️  Failed Assignments:');
                foreach ($results['details'] as $detail) {
                    if ($detail['status'] === 'failed' || $detail['status'] === 'error') {
                        $reason = $detail['reason'] ?? 'Unknown error';
                        $this->warn("  • Delivery #{$detail['delivery_id']}: {$reason}");
                    }
                }
            }
            
        } else {
            $this->info('✅ No pending deliveries to assign');
        }

        $this->newLine();
        $this->info('🎯 Auto-assignment completed!');

        return 0;
    }
}
