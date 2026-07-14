<aside class="w-64 h-full bg-white border-r border-gray-200 flex flex-col shrink-0"> 
    {{-- Logo --}}
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center">
                <img src="{{ asset('simbing-logo.png') }}" alt="SIMBING Logo" class="w-10 h-10 rounded-lg object-contain">
            </div>  
            <div>
                <h1 class="font-bold text-gray-900">SIMBING</h1>
                <p class="text-xs text-gray-500">Sistem Manajemen Bimbingan</p>
            </div>
        </div>
    </div>

    {{-- Role Switcher --}}
    <!-- <div class="p-4 border-b border-gray-200">
        <button
            wire:click="toggleRole"
            class="w-full px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors flex items-center justify-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            {{ $role === 'student' ? 'Switch to Dosen' : 'Switch to Mahasiswa' }}
        </button>
    </div> -->

    {{-- Navigation --}}
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-2">
            @foreach($menuItems as $item)
                @php
                    $isActive = request()->is(ltrim($item['path'], '/')) || (request()->is('/') && $item['path'] === '/student');
                @endphp
                <li>
                    <a
                        href="{{ $item['path'] }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $isActive ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}"
                        wire:navigate
                        @click="sidebarOpen = false"
                    >
                        @if($item['icon'] === 'home')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        @elseif($item['icon'] === 'file-text')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        @elseif($item['icon'] === 'message-square')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                        @elseif($item['icon'] === 'calendar')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                        @elseif($item['icon'] === 'user')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        @endif
                        <span class="text-sm font-medium">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- User Profile --}}
    <div class="p-4 border-t border-gray-200">
        <a href="/profile" wire:navigate @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-gray-50 transition-colors">
            @if($userAvatar)
                <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-100 flex-shrink-0" referrerpolicy="no-referrer">
            @else
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 ring-2 ring-blue-100">
                    {{ $userInitials }}
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $userName }}</p>
                <p class="text-xs text-gray-500">{{ $userRoleLabel }}</p>
            </div>
        </a>
    </div>

    {{-- Logout --}}
    <div class="px-4 pb-4">
        <button
            wire:click="logout"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors w-full"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" /></svg>
            <span class="text-sm font-medium">Keluar</span>
        </button>
    </div>
</aside>
