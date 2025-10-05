<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full text-center">
        <div class="mb-8">
            <i class="fas fa-exclamation-triangle text-9xl text-red-600 animate-pulse"></i>
        </div>

        <h1 class="text-9xl font-bold text-red-600 mb-4">500</h1>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Server Error</h2>
        <p class="text-xl text-gray-600 mb-8">
            Something went wrong on our end.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg shadow-lg hover:bg-red-700 transition">
                <i class="fas fa-home mr-2"></i>
                Go Home
            </a>
            
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-6 py-3 bg-white text-red-600 font-semibold rounded-lg shadow-lg hover:bg-gray-50 transition border-2 border-red-600">
                <i class="fas fa-sync-alt mr-2"></i>
                Try Again
            </button>
        </div>

        @if(config('app.debug') && isset($exception))
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6 text-left">
                <p class="text-sm text-red-800 font-mono">{{ $exception->getMessage() }}</p>
            </div>
        @endif
    </div>
</body>
</html>
