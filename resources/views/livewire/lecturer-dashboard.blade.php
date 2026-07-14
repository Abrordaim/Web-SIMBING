<div class="p-4 sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Dashboard Dosen</h1>
        <p class="text-gray-600 text-sm sm:text-base">Selamat datang kembali, {{ $lecturerName }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        @foreach($stats as $stat)
            @php
                $colorClasses = [
                    'blue' => 'bg-blue-50 text-blue-600',
                    'yellow' => 'bg-yellow-50 text-yellow-600',
                    'green' => 'bg-green-50 text-green-600',
                    'purple' => 'bg-purple-50 text-purple-600',
                ];
            @endphp
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center {{ $colorClasses[$stat['color']] }}">
                        @if($stat['icon'] === 'users')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                        @elseif($stat['icon'] === 'clock')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        @elseif($stat['icon'] === 'file-text')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        @elseif($stat['icon'] === 'check-circle')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        @endif
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">{{ $stat['value'] }}</h3>
                <p class="text-xs sm:text-sm text-gray-600">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Students List --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Mahasiswa Bimbingan</h2>
                </div>

                <div class="space-y-4">
                    @foreach($students as $student)
                        <div class="border border-gray-200 rounded-lg p-4 sm:p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3 gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <h3 class="font-bold text-gray-900">{{ $student['name'] }}</h3>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full flex-shrink-0">{{ $student['nim'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-3">{{ $student['title'] }}</p>
                                </div>
                                @if($student['pendingReviews'] > 0)
                                    <span class="px-2 sm:px-3 py-1 bg-red-50 text-red-700 text-xs font-medium rounded-full border border-red-200 flex-shrink-0">
                                        {{ $student['pendingReviews'] }} perlu review
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 mb-3">
                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs text-gray-600">Progress</span>
                                        <span class="text-xs font-bold text-blue-600">{{ $student['progress'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $student['progress'] }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs text-gray-500">Aktivitas terakhir: {{ $student['lastActivity'] }}</p>
                                <a href="/student-detail/{{ $student['id'] }}" wire:navigate class="px-3 sm:px-4 py-2 bg-blue-600 text-white text-xs sm:text-sm rounded-lg font-medium hover:bg-blue-700 transition-colors flex-shrink-0">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Pending Reviews --}}
        <div>
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Perlu Direview</h2>
                </div>
                <div class="space-y-4">
                    @foreach($pendingSubmissions as $submission)
                        @php
                            $priorityColors = [
                                'high' => 'bg-red-50 text-red-700 border-red-200',
                                'medium' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'low' => 'bg-blue-50 text-blue-700 border-blue-200',
                            ];
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2 gap-2">
                                <h3 class="font-medium text-gray-900 text-sm">{{ $submission['title'] }}</h3>
                                <span class="px-2 py-1 rounded-full text-xs font-medium border flex-shrink-0 {{ $priorityColors[$submission['priority']] }}">
                                    {{ $submission['priority'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 mb-2 font-medium">{{ $submission['student'] }}</p>
                            <p class="text-xs text-gray-500 mb-3">{{ $submission['submittedAt'] }}</p>
                            <button class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                Review Sekarang
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 sm:p-6 border border-blue-200">
                <h3 class="font-bold text-gray-900 mb-2">Reminder</h3>
                @if($thisWeekMeetingsCount > 0)
                    <p class="text-sm text-gray-700 mb-4">
                        Anda memiliki <strong>{{ $thisWeekMeetingsCount }}</strong> konsultasi yang dikonfirmasi minggu ini.
                    </p>
                @else
                    <p class="text-sm text-gray-700 mb-4">
                        Tidak ada konsultasi terjadwal minggu ini.
                    </p>
                @endif
                <a href="/schedule" wire:navigate class="text-sm text-blue-600 font-medium hover:text-blue-700">Lihat Jadwal →</a>
            </div>
        </div>
    </div>
</div>
