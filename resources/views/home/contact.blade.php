@extends('layouts.public')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Contact Us</h2>
        <p class="text-gray-600 mb-6">If you have questions about our drone delivery service or need support, please use the form below or email us at <a href="mailto:support@dronedelivery.local" class="text-purple-600">support@dronedelivery.local</a>.</p>

        <form action="#" method="POST" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="Your name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="you@example.com">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="How can we help?"></textarea>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Send Message</button>
                <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to homepage</a>
            </div>
        </form>
    </div>
</div>
@endsection
