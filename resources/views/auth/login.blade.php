@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h2>
    <p class="text-gray-600 mb-6">Please login to your account</p>

    {{-- READ: Session status messages --}}
    @if (session('status'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('status') }}
        </div>
    @endif

    {{-- INSERT: Login form to authenticate user --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope mr-1"></i>Email Address
            </label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="your.email@example.com"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-1"></i>Password
            </label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
        >
            <i class="fas fa-sign-in-alt mr-2"></i>
            Sign In
        </button>
    </form>
</div>
@endsection

@section('footer-links')
    <p class="text-gray-600">
        Don't have an account? 
        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            Register here
        </a>
    </p>
@endsection
