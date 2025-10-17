@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
    <p class="text-gray-600 mb-6">Enter your new password below</p>

    {{-- UPDATE: Reset user password --}}
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

    <!-- Password Reset Token -->
    <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope mr-1"></i>Email Address
            </label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email', request()->email) }}" 
                required 
                autofocus 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="your.email@example.com"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- New Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-1"></i>New Password
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

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-1"></i>Confirm Password
            </label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="••••••••"
            >
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
        >
            <i class="fas fa-key mr-2"></i>
            Reset Password
        </button>
    </form>
</div>
@endsection
