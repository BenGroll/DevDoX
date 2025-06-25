<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DevDox</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/ui@0.7.2/dist/tailwind-ui.min.css">
</head>
<body class="bg-white text-gray-800">
    <div id="app" class="h-screen flex flex-col">
        <!-- Top Bar -->
        <header class="flex items-center justify-between px-4 py-2 border-b bg-gray-100">
            <div class="flex gap-4 items-center">
                <span class="font-bold text-lg">DevDox</span>
                @include('partials.version_picker')
            </div>
            @include('partials.search')
        </header>

        <!-- Main Layout -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 border-r bg-gray-50 overflow-y-auto">
                @include('partials.navigation')
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                @include('partials.content')
            </main>
        </div>
    </div>
</body>
</html>
