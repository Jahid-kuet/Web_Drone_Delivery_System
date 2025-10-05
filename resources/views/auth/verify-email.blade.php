@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
<div class="p-8">
    <div class="text-center mb-6">
        <div class="inline-block p-4 bg-blue-100 rounded-full mb-4">
            <i class="fas fa-envelope-open-text text-4xl text-blue-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h2>
        <p class="text-gray-600">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        </p>
    </div>

    {{-- READ: Verification link sent status --}}
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
            <i class="fas fa-check-circle mr-2"></i>
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="space-y-3">
        {{-- INSERT: Resend verification email --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-paper-plane mr-2"></i>
                Resend Verification Email
            </button>
        </form>

        {{-- DELETE: Logout session --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-sign-out-alt mr-2"></i>
                Log Out
            </button>
        </form>
    </div>
</div>
@endsection
