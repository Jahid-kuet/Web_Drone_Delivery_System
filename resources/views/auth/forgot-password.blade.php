@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
    <p class="text-gray-600 mb-6">No problem. Just let us know your email address and we will email you a password reset link.</p>

    {{-- READ: Session status messages --}}
    @if (session('status'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('status') }}
        </div>
    @endif

    {{-- INSERT: Send password reset email --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
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

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
        >
            <i class="fas fa-paper-plane mr-2"></i>
            Email Password Reset Link
        </button>
    </form>
</div>
@endsection

@section('footer-links')
    <p class="text-gray-600">
        Remember your password? 
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            Back to login
        </a>
    </p>
@endsection
