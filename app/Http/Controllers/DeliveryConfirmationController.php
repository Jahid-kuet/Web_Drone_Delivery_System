<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeliveryConfirmationController extends Controller
{
    /**
     * Generate OTP for delivery
     */
    public function generateOTP(Request $request, $deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        // Check if delivery is in appropriate status
        if (!in_array($delivery->status, ['landed', 'approaching_destination', 'in_transit'])) {
            return response()->json([
                'success' => false,
                'message' => 'OTP can only be generated for deliveries that are approaching or landed',
            ], 400);
        }

        try {
            $otp = $delivery->generateOTP();

            // TODO: Send OTP via SMS to recipient
            // $this->sendOTPSMS($delivery->recipient_phone, $otp);

            return response()->json([
                'success' => true,
                'message' => 'OTP generated successfully',
                'otp' => $otp, // In production, don't return OTP in response - only send via SMS
                'expires_at' => $delivery->otp_expires_at,
                'expires_in_minutes' => now()->diffInMinutes($delivery->otp_expires_at),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request, $deliveryId)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'verified_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $delivery = Delivery::findOrFail($deliveryId);
        $result = $delivery->verifyOTP($request->otp, $request->verified_by);

        $statusCode = $result['success'] ? 200 : 400;

        return response()->json($result, $statusCode);
    }

    /**
     * Get OTP status
     */
    public function getOTPStatus($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);
        $status = $delivery->getOTPStatus();

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOTP($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        // Check if already verified
        if ($delivery->otp_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery already verified',
            ], 400);
        }

        try {
            $otp = $delivery->resendOTP();

            // TODO: Send OTP via SMS
            // $this->sendOTPSMS($delivery->recipient_phone, $otp);

            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully',
                'otp' => $otp, // Remove in production
                'expires_at' => $delivery->otp_expires_at,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload delivery proof photo
     */
    public function uploadPhoto(Request $request, $deliveryId)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $delivery = Delivery::findOrFail($deliveryId);

        try {
            // Store photo
            $photo = $request->file('photo');
            $filename = 'delivery_' . $deliveryId . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('delivery-proofs', $filename, 'public');

            // Update delivery record
            $delivery->update([
                'delivery_photo_path' => $path,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'delivery_notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'data' => [
                    'photo_path' => $path,
                    'photo_url' => Storage::url($path),
                    'uploaded_at' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload recipient signature
     */
    public function uploadSignature(Request $request, $deliveryId)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string', // Base64 encoded signature
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $delivery = Delivery::findOrFail($deliveryId);

        try {
            // Decode base64 signature
            $signatureData = $request->signature;
            
            // Remove data:image/png;base64, prefix if present
            $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            $signatureData = base64_decode($signatureData);

            if (!$signatureData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature data',
                ], 400);
            }

            // Store signature
            $filename = 'signature_' . $deliveryId . '_' . time() . '.png';
            $path = 'delivery-signatures/' . $filename;
            Storage::disk('public')->put($path, $signatureData);

            // Update delivery record
            $delivery->update([
                'recipient_signature_path' => $path,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Signature uploaded successfully',
                'data' => [
                    'signature_path' => $path,
                    'signature_url' => Storage::url($path),
                    'uploaded_at' => now(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload signature: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete delivery confirmation (OTP verified + photo uploaded)
     */
    public function completeConfirmation(Request $request, $deliveryId)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:20',
            'signature' => 'nullable|string', // Base64
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $delivery = Delivery::findOrFail($deliveryId);

        try {
            // Step 1: Verify OTP
            $otpResult = $delivery->verifyOTP($request->otp, $request->recipient_name);
            
            if (!$otpResult['success']) {
                return response()->json($otpResult, 400);
            }

            // Step 2: Upload photo
            $photo = $request->file('photo');
            $photoFilename = 'delivery_' . $deliveryId . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('delivery-proofs', $photoFilename, 'public');

            // Step 3: Upload signature (if provided)
            $signaturePath = null;
            if ($request->has('signature')) {
                $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $request->signature);
                $signatureData = base64_decode($signatureData);
                
                if ($signatureData) {
                    $signatureFilename = 'signature_' . $deliveryId . '_' . time() . '.png';
                    $signaturePath = 'delivery-signatures/' . $signatureFilename;
                    Storage::disk('public')->put($signaturePath, $signatureData);
                }
            }

            // Step 4: Update delivery as completed
            $delivery->update([
                'delivery_photo_path' => $photoPath,
                'recipient_signature_path' => $signaturePath,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'delivery_notes' => $request->notes,
                'status' => Delivery::STATUS_DELIVERED,
                'delivery_completed_time' => now(),
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            // Step 5: Update drone status
            if ($delivery->drone) {
                $delivery->drone->update([
                    'status' => 'returning',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Delivery confirmed successfully',
                'data' => [
                    'delivery_id' => $delivery->id,
                    'status' => $delivery->status,
                    'verified_at' => $delivery->verified_at,
                    'photo_url' => Storage::url($photoPath),
                    'signature_url' => $signaturePath ? Storage::url($signaturePath) : null,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete confirmation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get delivery confirmation details
     */
    public function getConfirmationDetails($deliveryId)
    {
        $delivery = Delivery::with(['hospital', 'drone', 'medicalSupply'])
            ->findOrFail($deliveryId);

        $otpStatus = $delivery->getOTPStatus();

        return response()->json([
            'success' => true,
            'data' => [
                'delivery' => [
                    'id' => $delivery->id,
                    'delivery_number' => $delivery->delivery_number,
                    'status' => $delivery->status,
                    'hospital' => $delivery->hospital->name ?? null,
                    'medical_supply' => $delivery->medicalSupply->name ?? null,
                ],
                'otp_status' => $otpStatus,
                'photo_uploaded' => !empty($delivery->delivery_photo_path),
                'photo_url' => $delivery->delivery_photo_path ? Storage::url($delivery->delivery_photo_path) : null,
                'signature_uploaded' => !empty($delivery->recipient_signature_path),
                'signature_url' => $delivery->recipient_signature_path ? Storage::url($delivery->recipient_signature_path) : null,
                'recipient_name' => $delivery->recipient_name,
                'recipient_phone' => $delivery->recipient_phone,
                'delivery_notes' => $delivery->delivery_notes,
                'is_verified' => $delivery->is_verified,
                'verified_at' => $delivery->verified_at,
                'completed_at' => $delivery->delivery_completed_time,
            ],
        ]);
    }
}

