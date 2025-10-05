<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle user authentication including login, registration,
| password reset, and email verification.
|
*/

// ==================== GUEST ROUTES (Not Authenticated) ====================

Route::middleware('guest')->group(function () {
    
    // Login Routes
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', function (Illuminate\Http\Request $request) {
        // Validate credentials
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to log the user in
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect to intended page or dashboard
            return redirect()->intended('/admin/dashboard');
        }

        // If authentication fails, redirect back with error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    })->name('login.post');
    
    // Registration Routes
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::post('/register', function () {
        // Registration logic
    })->name('register.post');
    
    // Password Reset Routes
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    
    Route::post('/forgot-password', function () {
        // Send password reset link logic
    })->name('password.email');
    
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    
    Route::post('/reset-password', function () {
        // Reset password logic
    })->name('password.update');
});

// ==================== AUTHENTICATED ROUTES ====================

Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', function (Illuminate\Http\Request $request) {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    })->name('logout');
    
    // Email Verification Routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function () {
        // Email verification logic
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function () {
        // Resend verification email logic
    })->middleware('throttle:6,1')->name('verification.send');
    
    // Password Confirmation
    Route::get('/confirm-password', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');
    
    Route::post('/confirm-password', function () {
        // Confirm password logic
    });
});

/*
|--------------------------------------------------------------------------
| Social Authentication (Optional)
|--------------------------------------------------------------------------
|
| Routes for social authentication providers like Google, Facebook, etc.
| Uncomment and configure as needed.
|
*/

// Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
//     ->name('social.redirect');
//
// Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
//     ->name('social.callback');
