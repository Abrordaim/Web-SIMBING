<div class="p-4 sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Profil</h1>
            <p class="text-gray-600 text-sm sm:text-base">Informasi personal dan akademik</p>
        </div>
        {{-- <button
            wire:click="{{ $isEditing ? 'save' : 'toggleEdit' }}"
            class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
        >
            @if($isEditing)
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" /></svg>
                Simpan
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                Edit Profil
            @endif
        </button> --}}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 text-center">
                {{-- Avatar with upload --}}
                <div class="relative w-32 h-32 mx-auto mb-4 group">
                    @if($newAvatar)
                        {{-- Preview of newly selected photo --}}
                        <img src="{{ $newAvatar->temporaryUrl() }}" alt="Preview" class="w-32 h-32 rounded-full object-cover shadow-lg ring-4 ring-blue-100">
                    @elseif($avatarUrl)
                        {{-- Current avatar photo --}}
                        <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="w-32 h-32 rounded-full object-cover shadow-lg ring-4 ring-blue-100" referrerpolicy="no-referrer">
                    @else
                        {{-- Fallback initials --}}
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-4xl font-bold shadow-lg ring-4 ring-blue-100">
                            {{ strtoupper(substr($name, 0, 2)) }}
                        </div>
                    @endif

                    @if($isEditing)
                        {{-- Upload overlay --}}
                        <label for="avatar-upload" class="absolute inset-0 flex flex-col items-center justify-center bg-black/50 rounded-full cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <svg class="w-8 h-8 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                            </svg>
                            <span class="text-white text-xs font-medium">Ubah Foto</span>
                        </label>
                        <input id="avatar-upload" type="file" wire:model="newAvatar" accept="image/*" class="hidden">

                        {{-- Remove button --}}
                        @if($avatarUrl || $newAvatar)
                            <button
                                wire:click="removeAvatar"
                                type="button"
                                class="absolute -top-1 -right-1 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-colors z-10"
                                title="Hapus foto"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    @endif
                </div>

                {{-- Upload status --}}
                <div wire:loading wire:target="newAvatar" class="text-sm text-blue-600 mb-2">
                    <svg class="animate-spin inline w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mengunggah...
                </div>
                @error('newAvatar')
                    <p class="text-sm text-red-500 mb-2">{{ $message }}</p>
                @enderror

                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $name }}</h2>
                <p class="text-gray-600 mb-4">NIM: {{ $nim }}</p>
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-center gap-2 text-gray-700 mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                        <span class="text-sm">{{ $department }}</span>
                    </div>
                    <div class="flex items-center justify-center gap-2 text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                        <span class="text-sm">{{ $faculty }}</span>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 mt-6">
                <h3 class="font-bold text-gray-900 mb-4">Statistik</h3>
                <div class="space-y-4">
                    @foreach($stats as $stat)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $stat['label'] }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $stat['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Profile Information --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Personal</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                            Nama Lengkap
                        </label>
                        <input type="text" wire:model="name" {{ !$isEditing ? 'disabled' : '' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $isEditing ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' : 'bg-gray-50' }} outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                            NIM
                        </label>
                        <input type="text" wire:model="nim" {{ !$isEditing ? 'disabled' : '' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $isEditing ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' : 'bg-gray-50' }} outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                            Email
                        </label>
                        <input type="email" wire:model="email" {{ !$isEditing ? 'disabled' : '' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $isEditing ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' : 'bg-gray-50' }} outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            Nomor Telepon
                        </label>
                        <input type="tel" wire:model="phone" {{ !$isEditing ? 'disabled' : '' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $isEditing ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' : 'bg-gray-50' }} outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                            Program Studi
                        </label>
                        <input type="text" wire:model="department" {{ !$isEditing ? 'disabled' : '' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $isEditing ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' : 'bg-gray-50' }} outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                            Fakultas
                        </label>
                        <input type="text" wire:model="faculty" {{ !$isEditing ? 'disabled' : '' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $isEditing ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' : 'bg-gray-50' }} outline-none">
                    </div>
                </div>
            </div>
            
            @if($role == 'student')
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 mt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Skripsi</h2>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Skripsi</label>
                        <textarea wire:model="thesisTitle" {{ !$isEditing ? 'disabled' : '' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg resize-none {{ $isEditing ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' : 'bg-gray-50' }} outline-none" rows="3"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Dosen Pembimbing --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                Dosen Pembimbing
                            </label>

                            @if($isEditing)
                                {{-- Multi-select lecturers --}}
                                <div class="space-y-2">
                                    {{-- Selected lecturers as tags --}}
                                    @if(!empty($selectedSupervisorIds))
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach($availableLecturers as $lecturer)
                                                @if(in_array($lecturer['id'], $selectedSupervisorIds))
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                                        {{ $lecturer['name'] }}
                                                        <button type="button" wire:click="toggleSupervisor({{ $lecturer['id'] }})" class="ml-1 hover:text-red-600 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Lecturer list --}}
                                    <div class="border border-gray-300 rounded-lg max-h-48 overflow-y-auto">
                                        @forelse($availableLecturers as $lecturer)
                                            <label
                                                wire:click="toggleSupervisor({{ $lecturer['id'] }})"
                                                class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-blue-50 transition-colors border-b border-gray-100 last:border-b-0 {{ in_array($lecturer['id'], $selectedSupervisorIds) ? 'bg-blue-50' : '' }}"
                                            >
                                                <div class="w-5 h-5 border-2 rounded flex items-center justify-center flex-shrink-0 {{ in_array($lecturer['id'], $selectedSupervisorIds) ? 'bg-blue-600 border-blue-600' : 'border-gray-300' }}">
                                                    @if(in_array($lecturer['id'], $selectedSupervisorIds))
                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $lecturer['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $lecturer['department'] }}</p>
                                                </div>
                                            </label>
                                        @empty
                                            <div class="px-4 py-3 text-sm text-gray-500 text-center">Belum ada dosen terdaftar</div>
                                        @endforelse
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Pilih satu atau lebih dosen pembimbing</p>
                                </div>
                            @else
                                {{-- Display mode: show selected supervisors --}}
                                @if(!empty($supervisorNames))
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($supervisorNames as $name)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-800 text-sm font-medium rounded-lg border border-gray-200">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                                {{ $name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <input type="text" value="Belum ada dosen pembimbing" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 outline-none">
                                @endif
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                                Mulai Bimbingan
                            </label>
                            <input type="text" value="{{ $startDate }}" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 outline-none">
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4"> 
                <button
                    wire:click="{{ $isEditing ? 'save' : 'toggleEdit' }}"
                    class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
                >
                    @if($isEditing)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" /></svg>
                        Simpan
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                        Edit Profil
                    @endif
                </button>
            </div>

            {{-- @if($isEditing)
                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                    <button wire:click="toggleEdit" class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors text-center">Batal</button>
                    <button wire:click="save" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" /></svg>
                        Simpan Perubahan
                    </button>
                </div>
            @endif --}}
        </div>
    </div>
</div>
