@php $isLecturer = $this->role === 'lecturer'; @endphp

@if(!$isLecturer)
{{-- STUDENT VIEW --}}
<div class="flex flex-col md:flex-row h-full bg-gray-50 overflow-hidden" x-data="{ activePanel: 'pdf' }">
    {{-- Mobile Tab Switcher --}}
    <div class="md:hidden bg-white border-b border-gray-200 flex flex-shrink-0">
        <button
            @click="activePanel = 'pdf'"
            :class="activePanel === 'pdf' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600'"
            class="flex-1 py-2.5 text-sm font-medium flex items-center justify-center gap-2 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            Dokumen
        </button>
        <button
            @click="activePanel = 'feedback'"
            :class="activePanel === 'feedback' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600'"
            class="flex-1 py-2.5 text-sm font-medium flex items-center justify-center gap-2 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
            Feedback
        </button>
    </div>

    {{-- PDF Viewer (Left/Center) --}}
    <main
        class="flex-1 flex flex-col min-w-0 overflow-hidden"
        :class="{ 'hidden md:flex': activePanel !== 'pdf' }"
    >
        {{-- Student header --}}
        <div class="bg-white border-b border-gray-200 px-2 md:px-6 py-1 flex-shrink-0">
            <div class="flex items-center gap-3">
                {{-- <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                </div> --}}
                <div>
                    <h2 class="font-bold text-gray-900 text-sm">Revisi & Feedback </h2>
                    <p class="text-xs text-gray-500">{{ auth()->user()->name ?? '' }} · {{ auth()->user()->student->nim ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- PDF embed --}}
        @include('livewire.partials.pdf-viewer', ['selectedPdfUrl' => $selectedPdfUrl, 'selectedThread' => $selectedThread])
    </main>

    {{-- Right Sidebar: Feedback & Revisi --}}
    <div
        class="md:flex flex-col "
        :class="{ 'hidden md:flex': activePanel !== 'feedback', 'flex': activePanel === 'feedback' }"
    >
        @include('livewire.partials.feedback-sidebar', [
            'threads' => $filteredThreads,
            'studentId' => auth()->user()->student->id ?? 0,
            'isLecturer' => false,
            'statusCfg' => $statusCfg,
        ])
    </div>
</div>

@else
{{-- LECTURER VIEW --}}

@if(!$selectedStudentId)
    {{-- ============================================ --}}
    {{-- STUDENT LIST VIEW (No student selected yet) --}}
    {{-- ============================================ --}}
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-indigo-50/20">
        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-md border-b border-gray-200/60 sticky top-0 z-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 sm:py-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                        {{-- <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-200/50 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                        </div> --}}
                        <div class="min-w-0">
                            <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">Revisi & Feedback</h1>
                            <p class="text-xs sm:text-sm text-gray-500 truncate">Pilih mahasiswa bimbingan untuk melihat dokumen dan feedback</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs sm:text-sm font-medium">
                            {{ count($students) }} Mahasiswa
                        </span>
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="mt-4 relative">
                    <svg class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    <input type="text" wire:model.live="search" placeholder="Cari nama atau NIM mahasiswa..." class="w-full max-w-md pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition shadow-sm">
                </div>
            </div>
        </div>

        {{-- Student Cards Grid --}}
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                @foreach($filteredStudents as $s)
                    @php
                        $totalDocs = $s['totalDocs'] ?? 0;
                        $pendingCount = $s['pendingCount'] ?? 0;
                        $revisionCount = $s['revisionCount'] ?? 0;
                        $approvedCount = $s['approvedCount'] ?? 0;
                    @endphp

                    <button
                        wire:click="selectStudent({{ $s['id'] }})"
                        class="group relative bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 text-left transition-all duration-300 hover:shadow-xl hover:shadow-blue-100/50 hover:border-blue-300/60 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        {{-- Subtle gradient overlay on hover --}}
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/0 to-indigo-50/0 group-hover:from-blue-50/50 group-hover:to-indigo-50/30 transition-all duration-300 pointer-events-none"></div>

                        <div class="relative">
                            {{-- Top row: Avatar + Name + Status --}}
                            <div class="flex items-start gap-3 sm:gap-4 mb-3 sm:mb-4">
                                <div class="relative flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-blue-200/40 group-hover:shadow-lg group-hover:shadow-blue-300/50 transition-shadow duration-300">
                                        <img src="{{ $s['avatar'] }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover rounded-xl">
                                    </div>
                                    @if($s['status'] === 'warning')
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full border-2 border-white flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5 text-yellow-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-blue-700 transition-colors truncate">{{ $s['name'] }}</h3>
                                    <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $s['nim'] }}</p>
                                </div>
                                @if($s['unread'] > 0)
                                    <span class="w-6 h-6 bg-blue-600 text-white text-xs rounded-full flex items-center justify-center font-bold shadow-md shadow-blue-200/60 animate-pulse flex-shrink-0">{{ $s['unread'] }}</span>
                                @endif
                            </div>

                            {{-- Thesis title --}}
                            <p class="text-sm text-gray-600 mb-3 sm:mb-4 leading-relaxed line-clamp-2">{{ $s['title'] }}</p>

                            {{-- Stats row --}}
                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                    {{ $totalDocs }} dokumen
                                </span>
                                @if($pendingCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-medium">{{ $pendingCount }} menunggu</span>
                                @endif
                                @if($revisionCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg text-xs font-medium">{{ $revisionCount }} revisi</span>
                                @endif
                                @if($approvedCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-medium">{{ $approvedCount }} disetujui</span>
                                @endif
                            </div>

                            {{-- Last message preview --}}
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                                <p class="text-xs text-gray-400 truncate flex-1">
                                    <span class="text-gray-500">Terakhir:</span> {{ $s['lastMsg'] }}
                                </p>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $s['lastTime'] }}</span>
                            </div>
                        </div>

                        {{-- Arrow indicator --}}
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-1 group-hover:translate-x-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </div>
                    </button>
                @endforeach
            </div>

            @if(count($filteredStudents) === 0)
                <div class="flex flex-col items-center justify-center py-16 sm:py-20 text-gray-400">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Mahasiswa tidak ditemukan</p>
                    <p class="text-xs text-gray-400 mt-1">Coba kata kunci lain</p>
                </div>
            @endif
        </div>
    </div>

@else
    {{-- ============================================ --}}
    {{-- STUDENT DETAIL VIEW (Student selected)      --}}
    {{-- Same layout as student role                 --}}
    {{-- ============================================ --}}
    <div class="flex flex-col md:flex-row h-full bg-gray-50 overflow-hidden" x-data="{ activePanel: 'pdf' }">
        {{-- Mobile Tab Switcher --}}
        <div class="md:hidden bg-white border-b border-gray-200 flex flex-shrink-0">
            <button
                @click="activePanel = 'pdf'"
                :class="activePanel === 'pdf' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600'"
                class="flex-1 py-2.5 text-sm font-medium flex items-center justify-center gap-2 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                Dokumen
            </button>
            <button
                @click="activePanel = 'feedback'"
                :class="activePanel === 'feedback' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600'"
                class="flex-1 py-2.5 text-sm font-medium flex items-center justify-center gap-2 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                Feedback
            </button>
        </div>

        {{-- PDF Viewer (Left/Center) --}}
        <main
            class="flex-1 flex flex-col min-w-0 overflow-hidden"
            :class="{ 'hidden md:flex': activePanel !== 'pdf' }"
        >
            {{-- Header with back button + student info --}}
            <div class="bg-white border-b border-gray-200 px-4 md:px-6 py-3 flex-shrink-0">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    {{-- Back button --}}
                    <button
                        wire:click="backToStudentList"
                        class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-blue-100 flex items-center justify-center transition-colors group flex-shrink-0"
                        title="Kembali ke daftar mahasiswa"
                    >
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    </button>

                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-semibold flex-shrink-0 shadow-md shadow-blue-200/40">
                        <img src="{{ $selectedStudent['avatar'] }}" alt="{{ $selectedStudent['name'] }}" class="w-full h-full object-cover rounded-xl">
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-bold text-gray-900 text-sm truncate">{{ $selectedStudent['name'] }}</h2>
                        <p class="text-xs text-gray-400 truncate">{{ $selectedStudent['nim'] }}</p>
                    </div>
                    <div class="hidden sm:block ml-auto min-w-0 max-w-xs">
                        <p class="text-xs text-gray-500 truncate">{{ $selectedStudent['title'] }}</p>
                    </div>
                </div>
            </div>

            {{-- PDF embed --}}
            @include('livewire.partials.pdf-viewer', ['selectedPdfUrl' => $selectedPdfUrl, 'selectedThread' => $selectedThread])
        </main>

        {{-- Right Sidebar: Feedback & Revisi --}}
        <div
            class="md:flex flex-col"
            :class="{ 'hidden md:flex': activePanel !== 'feedback', 'flex': activePanel === 'feedback' }"
        >
            @include('livewire.partials.feedback-sidebar', [
                'threads' => $filteredThreads,
                'studentId' => $selectedStudentId,
                'isLecturer' => true,
                'statusCfg' => $statusCfg,
            ])
        </div>
    </div>
@endif

@endif
