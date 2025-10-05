<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full text-center">
        <div class="mb-8 relative">
            <div class="inline-block animate-bounce">
                <i class="fas fa-helicopter text-9xl text-indigo-600"></i>
            </div>
        </div>

        <h1 class="text-9xl font-bold text-indigo-600 mb-4">404</h1>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Page Not Found</h2>
        <p class="text-xl text-gray-600 mb-8">
            The page you're looking for doesn't exist.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:bg-indigo-700 transition">
                <i class="fas fa-home mr-2"></i>
                Go Home
            </a>
            
            <a href="{{ url('/login') }}" 
               class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg shadow-lg hover:bg-gray-50 transition border-2 border-indigo-600">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
            </a>
        </div>
    </div>
</body>
</html>
