<div class="p-4 sm:p-6 lg:p-8 min-h-screen bg-gray-50" x-data="{ showToast: false }" x-init="$wire.on('clear-toast', () => { setTimeout(() => $wire.clearToast(), 3000) })">
    @if($successToast)
    <div class="fixed top-6 right-6 z-50 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        <span class="text-sm font-medium">{{ $successToast }}</span>
    </div>
    @endif

    {{-- Back & Header --}}
    <div class="mb-6">
        <a href="/lecturer" wire:navigate class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            <span class="text-sm font-medium">Kembali ke Dashboard</span>
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">Detail Mahasiswa Bimbingan</h1>
                <p class="text-gray-500 text-sm">Kelola dan berikan feedback untuk pengajuan skripsi mahasiswa</p>
            </div>
            <a href="/revision" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                Lihat Semua Revisi & Feedback
            </a>
        </div>
    </div>

    {{-- Student Profile Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="font-bold text-gray-900 text-xl">{{ $student['name'] }}</h2>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $student['status'] === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
                            {{ $student['status'] === 'active' ? 'Aktif' : 'Perlu Perhatian' }}
                        </span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">NIM: {{ $student['nim'] }}</p>
                    <p class="text-gray-500 text-sm">{{ $student['email'] }}</p>
                </div>
            </div>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4 lg:border-l lg:border-gray-200 lg:pl-6">
                <div><p class="text-xs text-gray-400 mb-1">Semester</p><p class="font-semibold text-gray-800">Semester {{ $student['semester'] }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Mulai Bimbingan</p><p class="font-semibold text-gray-800">{{ $student['startDate'] }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Aktivitas Terakhir</p><p class="font-semibold text-gray-800">{{ $student['lastActivity'] }}</p></div>
            </div>
            <div class="lg:border-l lg:border-gray-200 lg:pl-6 min-w-[160px]">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Progress</span>
                    <span class="font-bold text-gray-900">{{ $student['progress'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all" style="width: {{ $student['progress'] }}%"></div>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-gray-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                <div><span class="text-xs text-gray-600 block mb-0.5">Judul Skripsi</span><p class="text-gray-800 font-medium">{{ $student['title'] }}</p></div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 mb-6 shadow-sm overflow-x-auto">
        <button wire:click="setTab('submissions')" class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap {{ $activeTab === 'submissions' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            Dokumen & Pengajuan
        </button>
        <button wire:click="setTab('timeline')" class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap {{ $activeTab === 'timeline' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
            Riwayat
        </button>
    </div>

    {{-- Submissions Tab --}}
    @if($activeTab === 'submissions')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 gap-1">
            <h3 class="font-bold text-gray-900">Daftar Pengajuan ({{ count($student['submissions']) }} dokumen)</h3>
            <span class="text-sm text-gray-500">{{ collect($student['submissions'])->where('status', 'pending')->count() }} menunggu review</span>
        </div>
        @foreach($student['submissions'] as $sub)
            @php
                $statusInfo = $statusConfig[$sub['status']];
                $isExpanded = $expandedSubmission === $sub['id'];
                $savedDecision = $submittedDecisions[$sub['id']] ?? null;
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h4 class="font-semibold text-gray-900">{{ $sub['title'] }}</h4>
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $sub['type'] }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <span>{{ $sub['submittedAt'] }}</span>
                                    <span>{{ $sub['fileSize'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($savedDecision)
                                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border {{ $decisionColors[$savedDecision['decision']] }}">{{ $decisionLabels[$savedDecision['decision']] }}</span>
                            @else
                                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border {{ $statusInfo['color'] }}">{{ $statusInfo['label'] }}</span>
                            @endif
                            <button wire:click="toggleSubmission({{ $sub['id'] }})" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                @if($isExpanded)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                @endif
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                        {{-- <button class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Unduh Dokumen
                        </button> --}}
                        @if($sub['status'] === 'pending' && !$savedDecision)
                            <button wire:click="openDecisionPanel({{ $sub['id'] }})" class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors">Berikan Keputusan</button>
                        @endif
                        @if($savedDecision)
                            <button wire:click="openDecisionPanel({{ $sub['id'] }})" class="flex items-center gap-1.5 px-3 py-1.5 border border-blue-300 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-50 transition-colors">Ubah Keputusan</button>
                        @endif
                    </div>
                </div>
                @if($isExpanded)
                <div class="border-t border-gray-100 bg-gray-50 p-5 space-y-3">
                    @if($sub['feedback'])
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Feedback Sebelumnya</p>
                            <p class="text-sm text-gray-700">{{ $sub['feedback'] }}</p>
                        </div>
                    @endif
                    @if($savedDecision)
                        <div class="rounded-lg p-4 border {{ $decisionColors[$savedDecision['decision']] }}">
                            <p class="text-xs font-semibold mb-2">Keputusan Anda: {{ $decisionLabels[$savedDecision['decision']] }}</p>
                            @if($savedDecision['feedback'])<p class="text-sm">{{ $savedDecision['feedback'] }}</p>@endif
                        </div>
                    @endif
                    @if(!$sub['feedback'] && !$savedDecision)
                        <p class="text-sm text-gray-400 italic text-center py-2">Belum ada feedback untuk dokumen ini.</p>
                    @endif
                </div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    {{-- Timeline Tab --}}
    @if($activeTab === 'timeline')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-6">Riwayat Aktivitas Bimbingan</h3>
        <div class="relative">
            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            <div class="space-y-6">
                @foreach($student['timeline'] as $item)
                    @php
                        $colors = ['approved' => 'bg-green-100 border-green-300 text-green-600', 'revision' => 'bg-yellow-100 border-yellow-300 text-yellow-600', 'pending' => 'bg-blue-100 border-blue-300 text-blue-600', 'info' => 'bg-gray-100 border-gray-300 text-gray-600'];
                    @endphp
                    <div class="flex items-start gap-4 relative">
                        <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center flex-shrink-0 z-10 {{ $colors[$item['type']] ?? $colors['info'] }}">
                            @if($item['type'] === 'approved')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            @elseif($item['type'] === 'revision')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                            @elseif($item['type'] === 'pending')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                            @endif
                        </div>
                        <div class="flex-1 pb-2">
                            <p class="font-medium text-gray-900 text-sm">{{ $item['event'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item['date'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Decision Panel Modal --}}
    @if($showDecisionPanel && $selectedSubmission)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="closeDecisionPanel">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Berikan Keputusan Review</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ collect($student['submissions'])->firstWhere('id', $selectedSubmission)['title'] ?? '' }}</p>
                    </div>
                    <button wire:click="closeDecisionPanel" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100">✕</button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-3">Pilih Keputusan</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($decisionOptions as $opt)
                            <button wire:click="$set('decision', '{{ $opt['value'] }}')" class="flex flex-col items-start gap-2 p-3 rounded-xl border-2 text-left transition-all {{ $decision === $opt['value'] ? $opt['activeBg'] . ' shadow-md' : $opt['bg'] . ' hover:shadow-sm' }}">
                                <span class="text-sm font-semibold">{{ $opt['label'] }}</span>
                                <p class="text-xs leading-relaxed {{ $decision === $opt['value'] ? 'opacity-90' : 'opacity-70' }}">{{ $opt['description'] }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan / Feedback</label>
                    <textarea wire:model="feedbackText" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none" placeholder="Tambahkan catatan..." {{ !$decision ? 'disabled' : '' }}></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex gap-3 justify-end">
                <button wire:click="closeDecisionPanel" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                <button wire:click="submitDecision" {{ !$decision ? 'disabled' : '' }} class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                    Kirim Keputusan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
