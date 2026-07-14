<div class="p-4 sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Manajemen Jadwal Konsultasi</h1>
        <p class="text-gray-600 text-sm sm:text-base">Kelola dan pantau semua jadwal bimbingan dengan dosen pembimbing</p>
    </div>

    {{-- Upcoming Meetings --}}
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Jadwal Mendatang</h2>
            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">{{ count($upcomingMeetings) }} Jadwal</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($upcomingMeetings as $meeting)
                @php
                    $statusColors = [
                        'confirmed' => 'bg-green-50 text-green-700 border-green-200',
                        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                    ];
                    $statusLabels = [
                        'confirmed' => 'Dikonfirmasi',
                        'pending'   => 'Menunggu Konfirmasi',
                        'cancelled' => 'Ditolak ',
                    ];
                @endphp
                <div class="bg-white rounded-xl p-6 shadow-sm border {{ $meeting['status'] === 'cancelled' ? 'border-red-200 bg-red-50/30' : 'border-gray-200' }} hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-2">
                            @if($meeting['status'] === 'confirmed')
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            @elseif($meeting['status'] === 'cancelled')
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            @else
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                            @endif
                            <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$meeting['status']] ?? '' }}">{{ $statusLabels[$meeting['status']] ?? '' }}</span>
                        </div>
                        @if($meeting['type'] === 'online')
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                        @else
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-900 mb-2">{{ $meeting['title'] }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $meeting['lecturer'] }}</p>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            <span>{{ \Carbon\Carbon::parse($meeting['date'])->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <span>{{ $meeting['time'] }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            @if($meeting['type'] === 'online')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            @endif
                            <span>{{ $meeting['location'] }}</span>
                        </div>
                    </div>

                    {{-- Alasan Penolakan (jika ada) --}}
                    @if($meeting['status'] === 'cancelled' && !empty($meeting['notes']))
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
                            <p class="text-xs font-semibold text-orange-700 mb-1">💬 Alasan Penolakan Dosen:</p>
                            <p class="text-xs text-orange-600 italic">"{{ $meeting['notes'] }}"</p>
                            <p class="text-xs text-orange-500 mt-1">Edit jadwal di bawah untuk mengajukan ulang.</p>
                        </div>
                    {{-- @elseif($meeting['notes'])
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <p class="text-xs text-blue-700">{{ $meeting['notes'] }}</p>
                        </div> --}}
                    @endif

                    {{-- Action Buttons --}}
                    @if($isLecturer)
                        @if($meeting['status'] === 'pending')
                            {{-- Dosen: acc atau tolak pengajuan mahasiswa --}}
                            <div class="flex gap-2">
                                <button
                                    wire:click="confirmMeeting({{ $meeting['id'] }})"
                                    wire:confirm="Setujui jadwal konsultasi ini?"
                                    class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center justify-center gap-2 shadow-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Setujui
                                </button>
                                <button
                                    wire:click="openReject({{ $meeting['id'] }})"
                                    class="flex-1 px-3 py-2 bg-red-50 border border-red-300 text-red-700 text-sm rounded-lg font-medium hover:bg-red-100 transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                    Tolak
                                </button>
                            </div>
                        @else
                            {{-- Dosen: jadwal sudah dikonfirmasi, tidak ada aksi --}}
                            <div class="flex items-center gap-2 text-sm text-green-600 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Sudah dikonfirmasi
                            </div>
                        @endif
                    @else
                        {{-- Mahasiswa: edit dan batalkan --}}
                        <div class="flex gap-2">
                            <button wire:click="openEdit({{ $meeting['id'] }})" class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg font-medium hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                Edit
                            </button>
                            @if($meeting['status'] === 'pending')
                                <button
                                    wire:click="cancelMeeting({{ $meeting['id'] }})"
                                    wire:confirm="Batalkan pengajuan jadwal ini?"
                                    class="px-3 py-2 border border-red-300 text-red-700 text-sm rounded-lg font-medium hover:bg-red-50 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Past Meetings --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Riwayat Jadwal</h2>
            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">{{ count($pastMeetings) }} Jadwal</span>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pastMeetings as $meeting)
                            @php
                                $statusColors = [
                                    'completed' => 'bg-gray-50 text-gray-700 border-gray-200',
                                    'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                ];
                                $statusLabels = ['completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $meeting['title'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $meeting['lecturer'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($meeting['date'])->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $meeting['time'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        @if($meeting['type'] === 'online')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                        @endif
                                        <span>{{ $meeting['location'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$meeting['status']] ?? '' }}">{{ $statusLabels[$meeting['status']] ?? '' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-50 border border-green-300 text-green-800 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-pulse">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Edit Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeEdit">
            <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Edit Jadwal</h2>
                    <button wire:click="closeEdit" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="updateMeeting" class="space-y-4">
                    {{-- Judul --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Konsultasi</label>
                        <input type="text" wire:model="editTitle"
                            class="w-full px-4 py-2 border @error('editTitle') border-red-400 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            placeholder="Contoh: Bimbingan BAB 1">
                        @error('editTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" wire:model="editDate"
                            class="w-full px-4 py-2 border @error('editDate') border-red-400 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        @error('editDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Waktu Mulai --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                        <input type="time" wire:model="editTimeStart"
                            class="w-full px-4 py-2 border @error('editTimeStart') border-red-400 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        @error('editTimeStart') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-400">Sesi berdurasi 1 jam (waktu selesai dihitung otomatis)</p>
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input type="text" wire:model="editLocation"
                            class="w-full px-4 py-2 border @error('editLocation') border-red-400 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            placeholder="Contoh: Ruang Dosen 301">
                        @error('editLocation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tipe Pertemuan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pertemuan</label>
                        <select wire:model="editType"
                            class="w-full px-4 py-2 border @error('editType') border-red-400 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                            <option value="offline">Offline</option>
                            <option value="online">Online</option>
                        </select>
                        @error('editType') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Catatan (opsional) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea wire:model="editNotes" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                            placeholder="Catatan tambahan..."></textarea>
                    </div>

                    {{-- Info reset status --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 text-xs text-yellow-700 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        Setelah diedit, status jadwal akan direset ke <strong>&nbsp;Menunggu Konfirmasi</strong>.
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Simpan Perubahan
                        </button>
                        <button type="button" wire:click="closeEdit"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if($showRejectModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeReject">
            <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Tolak Jadwal</h2>
                            <p class="text-xs text-gray-500">Berikan alasan agar mahasiswa bisa menjadwalkan ulang</p>
                        </div>
                    </div>
                    <button wire:click="closeReject" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="submitReject" class="space-y-4">
                    {{-- Alasan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Alasan Penolakan
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="rejectReason"
                            rows="4"
                            placeholder="Contoh: Saya ada rapat dinas pada tanggal tersebut, mohon ajukan jadwal lain..."
                            class="w-full px-4 py-3 border @error('rejectReason') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition resize-none text-sm"
                        ></textarea>
                        @error('rejectReason')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">{{ strlen($rejectReason) }}/500 karakter</p>
                    </div>

                    {{-- Info email --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>
                        <p class="text-xs text-blue-700">Alasan ini akan dikirimkan ke email mahasiswa secara otomatis.</p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                            Konfirmasi Penolakan
                        </button>
                        <button type="button" wire:click="closeReject"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
