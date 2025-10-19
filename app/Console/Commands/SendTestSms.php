<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

class SendTestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone} {--message= : Custom message to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test SMS to verify SMS service configuration';

    protected SmsService $smsService;

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService): int
    {
        $this->smsService = $smsService;

        $phone = $this->argument('phone');
        $message = $this->option('message');

        // Display current configuration
        $this->info('SMS Service Configuration:');
        $this->info('==========================');
        
        $status = $this->smsService->getStatus();
        $this->table(
            ['Setting', 'Value'],
            [
                ['Enabled', $status['enabled'] ? '✅ Yes' : '❌ No'],
                ['Gateway', $status['gateway']],
                ['Configured', $status['configured'] ? '✅ Yes' : '❌ No'],
                ['Has Credentials', $status['has_credentials'] ? '✅ Yes' : '❌ No'],
            ]
        );

        $this->newLine();

        // Validate phone number
        if (!preg_match('/^01[0-9]{9}$/', $phone)) {
            $this->error('Invalid phone number format!');
            $this->warn('Phone number must be in Bangladesh format: 01XXXXXXXXX (11 digits)');
            return Command::FAILURE;
        }

        // Confirm sending
        if (!$this->confirm("Send test SMS to {$phone}?", true)) {
            $this->info('Test SMS cancelled.');
            return Command::SUCCESS;
        }

        $this->info('Sending test SMS...');

        // Send SMS
        try {
            if ($message) {
                $result = $this->smsService->send($phone, $message);
            } else {
                $result = $this->smsService->test($phone);
            }

            if ($result['success']) {
                $this->newLine();
                $this->info('✅ SMS sent successfully!');
                $this->line('Message ID: ' . $result['message_id']);
                
                if (!config('sms.enabled')) {
                    $this->warn('⚠️  SMS service is DISABLED. Check storage/logs/laravel.log for the message.');
                }
                
                return Command::SUCCESS;
            } else {
                $this->newLine();
                $this->error('❌ Failed to send SMS');
                $this->error('Error: ' . $result['message']);
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
