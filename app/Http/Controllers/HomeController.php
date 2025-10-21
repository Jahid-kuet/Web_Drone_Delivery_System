<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Drone;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the main public homepage
     */
    public function index()
    {
        // If user is logged in, redirect based on role and status
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user is pending approval - allow them to stay on home page
            if ($user->status === 'pending_approval') {
                // Don't redirect, let them see the home page with pending message
                $stats = [
                    'total_drones' => Drone::count(),
                    'active_deliveries' => Delivery::whereIn('status', ['pending', 'in_transit', 'picked_up'])->count(),
                    'completed_deliveries' => Delivery::where('status', 'completed')->count(),
                    'registered_hospitals' => Hospital::where('is_active', true)->count(),
                    'registered_users' => User::where('status', 'active')->count(),
                    'available_drones' => Drone::where('status', 'available')->count(),
                ];
                return view('home.index', compact('stats'));
            }
            
            // Check user role and redirect accordingly (only for active users)
            if ($user->hasRoleSlug('hospital_admin') || $user->hasRoleSlug('hospital_staff')) {
                // Check if user has hospital assigned
                if (!$user->hospital_id) {
                    // No hospital assigned yet, stay on home page
                    $stats = [
                        'total_drones' => Drone::count(),
                        'active_deliveries' => Delivery::whereIn('status', ['pending', 'in_transit', 'picked_up'])->count(),
                        'completed_deliveries' => Delivery::where('status', 'completed')->count(),
                        'registered_hospitals' => Hospital::where('is_active', true)->count(),
                        'registered_users' => User::where('status', 'active')->count(),
                        'available_drones' => Drone::where('status', 'available')->count(),
                    ];
                    return view('home.index', compact('stats'));
                }
                return redirect()->route('hospital.dashboard');
            } elseif ($user->hasRoleSlug('drone_operator')) {
                return redirect()->route('operator.dashboard');
            } else {
                // Admin and other roles go to admin dashboard
                return redirect()->route('admin.dashboard');
            }
        }

        // Get public statistics for homepage
        $stats = [
            'total_drones' => Drone::count(),
            'active_deliveries' => Delivery::whereIn('status', ['pending', 'in_transit', 'picked_up'])->count(),
            'completed_deliveries' => Delivery::where('status', 'completed')->count(),
            'registered_hospitals' => Hospital::where('is_active', true)->count(),
            'registered_users' => User::where('status', 'active')->count(),
            'available_drones' => Drone::where('status', 'available')->count(),
        ];

        return view('home.index', compact('stats'));
    }

    /**
     * Display about page
     */
    public function about()
    {
        return view('home.about');
    }

    /**
     * Display services page
     */
    public function services()
    {
        return view('home.services');
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view('home.contact');
    }
}
