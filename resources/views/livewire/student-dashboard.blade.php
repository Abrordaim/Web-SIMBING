<div class="p-4 sm:p-6 lg:p-8">
    {{-- Calendar Modal --}}
    @if($showAllSchedules)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="toggleSchedules">
        <div class="bg-white rounded-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-4 sm:p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-2xl font-bold text-gray-900">Semua Jadwal Konsultasi</h2>
                    <button wire:click="toggleSchedules" class="px-3 py-1.5 text-gray-600 hover:text-gray-900">✕</button>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Calendar --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900">
                                    {{ $monthNames[$calendarMonth] }} {{ $calendarYear }}
                                </h3>
                                <div class="flex gap-2">
                                    <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg">←</button>
                                    <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg">→</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-1 sm:gap-2">
                                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                                    <div class="text-center text-xs sm:text-sm font-medium text-gray-600 py-2">{{ $day }}</div>
                                @endforeach
                                @for($i = 0; $i < $firstDay; $i++)
                                    <div class="aspect-square"></div>
                                @endfor
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dateStr = sprintf('%d-%02d-%02d', $calendarYear, $calendarMonth + 1, $d);
                                        $dayMeetings = collect($allMeetings)->filter(fn($m) => $m['date'] === $dateStr)->values();
                                        $isToday = $d === 26 && $calendarMonth === 3;
                                    @endphp
                                    <div class="aspect-square border border-gray-200 rounded-lg p-1 sm:p-2 hover:bg-gray-50 transition-colors {{ $isToday ? 'bg-blue-50 border-blue-300' : '' }}">
                                        <div class="text-xs sm:text-sm font-medium mb-1 {{ $isToday ? 'text-blue-600' : 'text-gray-900' }}">{{ $d }}</div>
                                        @if($dayMeetings->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($dayMeetings->take(2) as $meeting)
                                                    <div class="text-xs px-1 py-0.5 rounded truncate {{ $meeting['status'] === 'completed' ? 'bg-gray-100 text-gray-600' : ($meeting['status'] === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                        {{ explode(' - ', $meeting['time'])[0] }}
                                                    </div>
                                                @endforeach
                                                @if($dayMeetings->count() > 2)
                                                    <div class="text-xs text-gray-500">+{{ $dayMeetings->count() - 2 }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    {{-- All Meetings List --}}
                    <div>
                        <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200">
                            <h3 class="font-bold text-gray-900 mb-4">Semua Jadwal</h3>
                            <div class="space-y-3 max-h-[400px] sm:max-h-[600px] overflow-y-auto">
                                @foreach($allMeetings as $meeting)
                                    <div class="border rounded-lg p-3 sm:p-4 {{ $meeting['status'] === 'completed' ? 'border-gray-200 bg-gray-50' : 'border-blue-200 bg-blue-50' }}">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-medium text-gray-900 text-sm">{{ $meeting['title'] }}</h4>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $meeting['status'] === 'completed' ? 'bg-gray-200 text-gray-700' : ($meeting['status'] === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ $meeting['status'] === 'completed' ? 'Selesai' : ($meeting['status'] === 'confirmed' ? 'Dikonfirmasi' : 'Menunggu') }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mb-2">{{ $meeting['lecturer'] }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-700 mb-1">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                                            <span>{{ \Carbon\Carbon::parse($meeting['date'])->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-700">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                            <span>{{ $meeting['time'] }} • {{ $meeting['location'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Dashboard Mahasiswa</h1>
        <p class="text-gray-600 text-sm sm:text-base">Selamat datang kembali, {{ $studentName }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        @foreach($stats as $stat)
            @php
                $colorClasses = [
                    'blue' => 'bg-blue-50 text-blue-600',
                    'yellow' => 'bg-yellow-50 text-yellow-600',
                    'green' => 'bg-green-50 text-green-600',
                    'red' => 'bg-red-50 text-red-600',
                ];
            @endphp
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center {{ $colorClasses[$stat['color']] }}">
                        @if($stat['icon'] === 'file-text')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        @elseif($stat['icon'] === 'clock')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        @elseif($stat['icon'] === 'check-circle')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        @elseif($stat['icon'] === 'alert-circle')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        @endif
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">{{ $stat['value'] }}</h3>
                <p class="text-xs sm:text-sm text-gray-600">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Progress Skripsi --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Progress Skripsi</h2>
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
                </div>
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                        <span class="text-sm font-bold text-blue-600">{{ $thesisProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $thesisProgress }}%"></div>
                    </div>
                    @if($thesisProgress === 0)
                        <p class="text-xs text-gray-400 mt-2">Progress dihitung otomatis saat BAB/Proposal disetujui dosen.</p>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Judul Skripsi</p>
                        <p class="text-sm font-medium text-gray-900">{{ $thesisTitle }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Dosen Pembimbing</p>
                        <p class="text-sm font-medium text-gray-900">{{ $supervisorNames }}</p>
                    </div>
                </div>
            </div>

            {{-- Recent Activities --}}
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Aktivitas Terbaru</h2>
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                        @php
                            $statusColors = [
                                'success' => 'bg-green-50 text-green-700 border-green-200',
                                'warning' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'pending' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'error'   => 'bg-red-50 text-red-700 border-red-200',
                            ];
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2 gap-2">
                                <h3 class="font-medium text-gray-900 text-sm sm:text-base">{{ $activity['title'] }}</h3>
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-medium border flex-shrink-0 {{ $statusColors[$activity['type']] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                    {{ $activity['status'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ $activity['date'] }}</p>
                            @if($activity['feedback'])
                                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">
                                    <strong>Feedback:</strong> {{ $activity['feedback'] }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- This Week's Schedule --}}
        <div>
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                        <h2 class="text-base font-bold text-gray-900">Jadwal Minggu Ini</h2>
                    </div>
                    <button wire:click="toggleSchedules" class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                    </button>
                </div>

                @if(count($thisWeekMeetings) > 0)
                    <div class="space-y-3">
                        @foreach($thisWeekMeetings as $meeting)
                            <div class="border border-blue-200 rounded-lg p-4 bg-blue-50 cursor-pointer hover:shadow-md transition-shadow" wire:click="toggleSchedules">
                                <div class="flex items-start justify-between mb-2 gap-2">
                                    <h3 class="font-medium text-gray-900 text-sm">{{ $meeting['title'] }}</h3>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0 {{ $meeting['status'] === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $meeting['status'] === 'confirmed' ? 'Dikonfirmasi' : 'Menunggu' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-700 mb-1">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                                    <span>{{ \Carbon\Carbon::parse($meeting['date'])->translatedFormat('D, d M') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-700">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    <span>{{ $meeting['time'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                        <p class="text-gray-500 text-sm">Tidak ada jadwal minggu ini</p>
                    </div>
                @endif

                <a href="/submission" wire:navigate class="block w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors text-center text-sm">
                    Ajukan Jadwal Baru
                </a>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 sm:p-6 border border-blue-200">
                <h3 class="font-bold text-gray-900 mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="/submission" wire:navigate class="w-full text-left px-4 py-3 bg-white rounded-lg hover:shadow-md transition-shadow flex items-center justify-between group block">
                        <span class="text-sm font-medium text-gray-700">Ajukan Bimbingan Baru</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                    </a>
                    <a href="/revision" wire:navigate class="w-full text-left px-4 py-3 bg-white rounded-lg hover:shadow-md transition-shadow flex items-center justify-between group block">
                        <span class="text-sm font-medium text-gray-700">Lihat Feedback</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
