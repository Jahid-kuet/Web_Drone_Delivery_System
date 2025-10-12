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
        // If user is logged in, redirect based on role
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check user role and redirect accordingly
            if ($user->hasRoleSlug('hospital_admin') || $user->hasRoleSlug('hospital_staff')) {
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
