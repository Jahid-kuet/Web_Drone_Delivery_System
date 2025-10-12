<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Display the public tracking page
     */
    public function index()
    {
        return view('tracking');
    }

    /**
     * Track a delivery by tracking number
     */
    public function track(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string'
        ]);

        $trackingNumber = $request->input('tracking_number');

        // Find delivery with relationships
        $delivery = Delivery::with([
            'deliveryRequest.hospital',
            'drone',
            'assignedPilot'
        ])
        ->where('tracking_number', $trackingNumber)
        ->first();

        if (!$delivery) {
            return back()->with('error', 'Tracking number not found. Please check and try again.');
        }

        return view('tracking', compact('delivery'));
    }
}
