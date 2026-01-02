<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Flood Prediction')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">
    <nav class="bg-white shadow-sm border-b border-slate-200">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-lg font-semibold">
                🌧️ Flood Prediction
            </h1>
            <div class="flex gap-4 text-sm">
                <a href="/" class="hover:text-blue-600">
                    Dashboard
                </a>
                <a href="/heatmap" class="hover:text-blue-600">
                    Heatmap
                </a>
            </div>
        </div>
    </nav>
    <main class="container mx-auto px-4 py-6">
        @yield('content')
    </main>
</body>
</html>
