<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SmsManagementController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Show SMS management dashboard
     */
    public function index()
    {
        $status = $this->smsService->getStatus();
        
        // Get recent SMS logs from database (if implemented) or log file
        $recentLogs = $this->getRecentSmsLogs(20);

        return view('admin.sms.index', [
            'status' => $status,
            'recentLogs' => $recentLogs,
            'gateways' => config('sms.gateways'),
            'currentGateway' => config('sms.default_gateway'),
        ]);
    }

    /**
     * Test SMS sending
     */
    public function test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^01[0-9]{9}$/'],
            'message' => 'nullable|string|max:160',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $phone = $request->phone;
            $message = $request->message ?? "Test SMS from Drone Delivery System at " . now()->format('Y-m-d H:i:s');

            $result = $this->smsService->send($phone, $message);

            if ($result['success']) {
                return back()->with('success', 'Test SMS sent successfully! Message ID: ' . $result['message_id']);
            } else {
                return back()->with('error', 'Failed to send SMS: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('SMS Test Error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Send bulk SMS
     */
    public function sendBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phones' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Parse phone numbers (comma or newline separated)
            $phones = preg_split('/[\s,;]+/', $request->phones);
            $phones = array_filter($phones); // Remove empty entries

            $results = [
                'success' => 0,
                'failed' => 0,
                'total' => count($phones),
            ];

            foreach ($phones as $phone) {
                $phone = trim($phone);
                
                // Validate phone format
                if (!preg_match('/^01[0-9]{9}$/', $phone)) {
                    $results['failed']++;
                    continue;
                }

                $result = $this->smsService->send($phone, $request->message);
                
                if ($result['success']) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                }

                // Rate limiting - wait 1 second between messages
                if ($results['success'] + $results['failed'] < $results['total']) {
                    sleep(1);
                }
            }

            return back()->with('success', 
                "Bulk SMS sent: {$results['success']} successful, {$results['failed']} failed out of {$results['total']} total."
            );

        } catch (\Exception $e) {
            Log::error('Bulk SMS Error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get SMS service status (AJAX)
     */
    public function status()
    {
        $status = $this->smsService->getStatus();
        
        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    /**
     * Get recent SMS logs from Laravel log file
     */
    protected function getRecentSmsLogs(int $limit = 20): array
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return [];
        }

        try {
            $lines = file($logFile);
            $smsLogs = [];
            
            // Parse log file in reverse (newest first)
            for ($i = count($lines) - 1; $i >= 0 && count($smsLogs) < $limit; $i--) {
                $line = $lines[$i];
                
                // Look for SMS-related log entries
                if (stripos($line, 'SMS') !== false) {
                    $smsLogs[] = [
                        'raw' => trim($line),
                        'timestamp' => $this->extractTimestamp($line),
                        'type' => $this->extractLogType($line),
                        'message' => $this->extractLogMessage($line),
                    ];
                }
            }
            
            return $smsLogs;
        } catch (\Exception $e) {
            Log::warning('Failed to read SMS logs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Extract timestamp from log line
     */
    protected function extractTimestamp(string $line): ?string
    {
        if (preg_match('/\[(.*?)\]/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extract log type (INFO, WARNING, ERROR)
     */
    protected function extractLogType(string $line): string
    {
        if (stripos($line, 'ERROR') !== false) return 'error';
        if (stripos($line, 'WARNING') !== false) return 'warning';
        return 'info';
    }

    /**
     * Extract log message
     */
    protected function extractLogMessage(string $line): string
    {
        // Remove timestamp and log level
        $message = preg_replace('/\[.*?\]\s*(local\.)?(INFO|WARNING|ERROR):\s*/', '', $line);
        return trim($message);
    }

    /**
     * Show SMS configuration page
     */
    public function configuration()
    {
        return view('admin.sms.configuration', [
            'config' => config('sms'),
            'gateways' => config('sms.gateways'),
        ]);
    }
}
