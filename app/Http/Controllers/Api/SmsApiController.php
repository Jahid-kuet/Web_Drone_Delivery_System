<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsApiController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Get SMS service status
     * 
     * @return \Illuminate\Http\JsonResponse
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
     * Send test SMS
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^01[0-9]{9}$/'],
            'message' => 'nullable|string|max:160',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $request->phone;
        $message = $request->message ?? "Test message from Drone Delivery API at " . now()->format('Y-m-d H:i:s');

        $result = $this->smsService->send($phone, $message);

        $statusCode = $result['success'] ? 200 : 500;

        return response()->json($result, $statusCode);
    }

    /**
     * Send custom SMS (admin only)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^01[0-9]{9}$/'],
            'message' => 'required|string|max:160',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->smsService->send($request->phone, $request->message);

        $statusCode = $result['success'] ? 200 : 500;

        return response()->json($result, $statusCode);
    }
}
