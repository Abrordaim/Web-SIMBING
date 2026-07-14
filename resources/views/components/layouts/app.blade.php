<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SIMBING - Sistem Manajemen Bimbingan Skripsi">
    <title>{{ $title ?? 'SIMBING - Sistem Manajemen Bimbingan Skripsi' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('simbing-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-gray-900 antialiased h-full overflow-hidden">
    <div class="flex h-full bg-gray-50" x-data="{ sidebarOpen: false }">

        {{-- Mobile Sidebar Overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black/50 lg:hidden"
            style="display: none;"
        ></div>

        {{-- Sidebar Wrapper --}}
        <div
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 lg:flex lg:flex-shrink-0"
        >
            <livewire:sidebar />
        </div>

        {{-- Main Content --}}
        <main class="flex-1 h-full overflow-y-auto relative min-w-0 flex flex-col">
            {{-- Mobile Top Bar --}}
            <div class="lg:hidden justify-between bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3 sticky top-0 z-10 flex-shrink-0">
                <button @click="sidebarOpen = true" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-blue-400 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('simbing-logo.png') }}" alt="SIMBING Logo" class="w-10 h-10 rounded-lg object-contain">
                    </div>
                    <span class="font-bold text-gray-900 text-sm">SIMBING</span>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
