<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Service Unavailable</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-yellow-50 to-amber-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full text-center">
        <div class="mb-8">
            <i class="fas fa-wrench text-9xl text-amber-600 animate-bounce"></i>
        </div>

        <h1 class="text-9xl font-bold text-amber-600 mb-4">503</h1>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Under Maintenance</h2>
        <p class="text-xl text-gray-600 mb-8">
            We'll be back soon!
        </p>

        <button onclick="location.reload()" 
                class="inline-flex items-center px-6 py-3 bg-amber-600 text-white font-semibold rounded-lg shadow-lg hover:bg-amber-700 transition">
            <i class="fas fa-sync-alt mr-2"></i>
            Check Again
        </button>
    </div>
</body>
</html>
