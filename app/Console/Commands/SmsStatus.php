<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

class SmsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SMS service configuration and status';

    protected SmsService $smsService;

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService): int
    {
        $this->smsService = $smsService;

        $this->info('SMS Service Status Report');
        $this->info('=========================');
        $this->newLine();

        // Get status
        $status = $this->smsService->getStatus();

        // Basic Configuration
        $this->line('<fg=cyan>Basic Configuration:</>');
        $this->table(
            ['Setting', 'Value', 'Status'],
            [
                [
                    'SMS Enabled',
                    config('sms.enabled') ? 'Yes' : 'No',
                    config('sms.enabled') ? '[OK]' : '[WARNING]'
                ],
                [
                    'Default Gateway',
                    config('sms.default_gateway'),
                    $status['gateway'] === 'log' ? '[WARNING] (Development Mode)' : '[OK]'
                ],
                [
                    'Gateway Configured',
                    $status['configured'] ? 'Yes' : 'No',
                    $status['configured'] ? '[OK]' : '[NO]'
                ],
                [
                    'Has Credentials',
                    $status['has_credentials'] ? 'Yes' : 'No',
                    $status['has_credentials'] ? '[OK]' : '[NO]'
                ],
            ]
        );

        $this->newLine();

        // OTP Configuration
        $this->line('<fg=cyan>OTP Configuration:</>');
        $this->table(
            ['Setting', 'Value'],
            [
                ['OTP Length', config('sms.otp.length') . ' digits'],
                ['OTP Expiry', config('sms.otp.expiry_minutes') . ' minutes'],
                ['Max Attempts', config('sms.otp.max_attempts')],
                ['Cooldown', config('sms.otp.cooldown_seconds') . ' seconds'],
            ]
        );

        $this->newLine();

        // Notification Settings
        $this->line('<fg=cyan>Notification Settings:</>');
        $this->table(
            ['Type', 'Enabled'],
            [
                [
                    'Delivery Status Updates',
                    config('sms.notifications.delivery_status_updates') ? '[OK] Yes' : '[NO] No'
                ],
                [
                    'OTP Generation',
                    config('sms.notifications.otp_generation') ? '[OK] Yes' : '[NO] No'
                ],
            ]
        );

        $this->newLine();

        // Available Gateways
        $this->line('<fg=cyan>Available Gateways:</>');
        $gateways = config('sms.gateways', []);
        $gatewayInfo = [];
        
        foreach ($gateways as $name => $config) {
            $hasCredentials = !empty($config['api_token'] ?? $config['api_key'] ?? '');
            $isCurrent = $name === config('sms.default_gateway');
            
            $gatewayInfo[] = [
                $name,
                $isCurrent ? '[OK] Current' : '-',
                $hasCredentials ? '[OK] Configured' : '[NO] Not Configured',
            ];
        }
        
        $this->table(['Gateway', 'Active', 'Status'], $gatewayInfo);

        $this->newLine();

        // Recommendations
        $this->line('<fg=cyan>Recommendations:</>');
        
        $recommendations = [];
        
        if (!config('sms.enabled')) {
            $recommendations[] = '⚠️  SMS service is DISABLED. Set SMS_ENABLED=true in .env to enable.';
        }
        
        if (config('sms.default_gateway') === 'log') {
            $recommendations[] = '⚠️  Using LOG gateway (development mode). Configure a real SMS gateway for production.';
        }
        
        if (!$status['has_credentials']) {
            $recommendations[] = '❌ No API credentials configured. Add credentials to .env file.';
        }
        
        if (config('app.env') === 'production' && !config('sms.enabled')) {
            $recommendations[] = '[ALERT] PRODUCTION environment with SMS DISABLED! Enable SMS for production use.';
        }
        
        if (empty($recommendations)) {
            $this->info('[OK] All systems operational!');
        } else {
            foreach ($recommendations as $recommendation) {
                $this->line($recommendation);
            }
        }

        $this->newLine();

        // Quick start guide
        if (!config('sms.enabled') || !$status['has_credentials']) {
            $this->line('<fg=yellow>Quick Start:</>');
            $this->line('1. Choose an SMS gateway (recommended: SSL Wireless)');
            $this->line('2. Get API credentials from gateway provider');
            $this->line('3. Update .env file:');
            $this->line('   SMS_ENABLED=true');
            $this->line('   SMS_GATEWAY=sslwireless');
            $this->line('   SMS_SSLWIRELESS_API_TOKEN=your_token');
            $this->line('   SMS_SSLWIRELESS_SID=your_sid');
            $this->line('4. Test: php artisan sms:test 01712345678');
            $this->newLine();
        }

        return Command::SUCCESS;
    }
}
