<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Flood Prediction')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">
    <nav class="bg-slate-800 shadow-lg border-b border-slate-700">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-lg font-semibold text-white">
                Flood Prediction
            </h1>
            <div class="flex gap-4 text-sm">
                <a href="/" class="text-slate-300 hover:text-blue-400">
                    Dashboard
                </a>
                <a href="/heatmap" class="text-slate-300 hover:text-blue-400">
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