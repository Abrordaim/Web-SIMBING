<div class="flex flex-col flex-1 overflow-hidden">
    {{-- Filter bar --}}
    <div class="flex items-center gap-1.5 px-6 py-3 border-b border-gray-200 bg-white flex-shrink-0">
        <span class="text-xs text-gray-500 mr-1">Filter:</span>
        @foreach(['all' => 'Semua', 'open' => 'Terbuka', 'resolved' => 'Selesai'] as $key => $label)
            <button
                wire:click="$set('filter', '{{ $key }}')"
                class="px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $this->filter === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                {{ $label }}
            </button>
        @endforeach
        <span class="ml-auto text-xs text-gray-400">{{ count($threads) }} thread</span>
    </div>

    {{-- Thread list --}}
    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
        @if(count($threads) === 0)
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-10 h-10 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                <p class="text-sm">Tidak ada thread untuk filter ini</p>
            </div>
        @endif

        @foreach($threads as $thread)
            @php
                $cfg = $statusCfg[$thread['docStatus']] ?? $statusCfg['pending'];
                $isExpanded = true;
            @endphp
            <div class="bg-white rounded-2xl border transition-all duration-200 overflow-hidden {{ $thread['resolved'] ? 'border-gray-200 opacity-80' : 'border-gray-200 shadow-sm' }}">
                {{-- Thread header --}}
                <div class="w-full px-5 py-4 flex items-center gap-3 text-left">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $thread['resolved'] ? 'bg-gray-100' : 'bg-blue-50' }}">
                        <svg class="w-4 h-4 {{ $thread['resolved'] ? 'text-gray-400' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold {{ $thread['resolved'] ? 'text-gray-500' : 'text-gray-900' }}">{{ $thread['docTitle'] }}</span>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full">{{ $thread['docType'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <span class="flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full border {{ $cfg['cls'] }}">
                                {{ $cfg['label'] }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $thread['docDate'] }}</span>
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                                {{ count($thread['comments']) }} komentar
                            </span>
                        </div>
                    </div>
                    @if($thread['resolved'])
                        <span class="flex items-center gap-1 text-xs text-green-700 bg-green-50 border border-green-200 px-2 py-1 rounded-full font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            Selesai
                        </span>
                    @endif
                </div>

                {{-- Comments --}}
                <div class="border-t border-gray-100">
                    <div class="px-5 py-4 space-y-0">
                        @foreach($thread['comments'] as $idx => $comment)
                            @php
                                $isFromLecturer = $comment['author'] === 'lecturer';
                                $isLast = $idx === count($thread['comments']) - 1;
                            @endphp
                            <div class="flex gap-3 group">
                                <div class="flex flex-col items-center">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center font-semibold text-xs flex-shrink-0 {{ $isFromLecturer ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                        {{-- {{ $comment['avatar'] }} --}}
                                        <img src="{{ $comment['avatar'] }}" alt="" class="w-full h-full rounded-full">
                                    </div>
                                    @if(!$isLast)
                                        <div class="w-0.5 bg-gray-200 flex-1 mt-1 mb-1" style="min-height: 16px"></div>
                                    @endif
                                </div>
                                <div class="flex-1 mb-3 {{ $isLast ? 'mb-1' : '' }}">
                                    <div class="rounded-2xl rounded-tl-sm px-4 py-3 {{ $isFromLecturer ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                                        <div class="flex items-center justify-between gap-2 mb-1.5 flex-wrap">
                                            <span class="text-xs font-semibold {{ $isFromLecturer ? 'text-blue-100' : 'text-gray-500' }}">
                                                {{ $comment['name'] }}
                                                @if($isFromLecturer)
                                                    <span class="ml-1.5 bg-white/20 px-1.5 py-0.5 rounded text-white text-[10px]">Dosen</span>
                                                @endif
                                            </span>
                                            <span class="text-[11px] {{ $isFromLecturer ? 'text-blue-200' : 'text-gray-400' }}">{{ $comment['time'] }}</span>
                                        </div>
                                        <p class="text-sm leading-relaxed">{{ $comment['text'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Reply / resolve --}}
                    @if(!$thread['resolved'])
                        <div class="px-5 pb-5">
                            <div class="flex gap-3 items-end">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center font-semibold text-xs flex-shrink-0 {{ $isLecturer ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $isLecturer ? 'AS' : 'SW' }}
                                </div>
                                <div class="flex-1 border border-gray-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-400 focus-within:border-transparent transition-all bg-white">
                                    <textarea
                                        rows="2"
                                        wire:model="replyTexts.{{ $thread['id'] }}"
                                        placeholder="Balas komentar... (Ctrl+Enter untuk kirim)"
                                        class="w-full px-4 pt-3 pb-1 text-sm resize-none outline-none text-gray-800 placeholder-gray-400"
                                    ></textarea>
                                    <div class="flex items-center justify-end px-3 pb-2.5 pt-1">
                                        <div class="flex items-center gap-2">
                                            @if($isLecturer)
                                                <button
                                                    wire:click="resolveThread({{ $thread['id'] }}, {{ $studentId }})"
                                                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                                    Tandai Selesai
                                                </button>
                                            @endif
                                            <button
                                                wire:click="sendReply({{ $thread['id'] }}, {{ $studentId }})"
                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                                                Balas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="px-5 pb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-green-600 text-xs font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                Thread ini sudah ditandai selesai
                            </div>
                            @if($isLecturer)
                                <button
                                    wire:click="resolveThread({{ $thread['id'] }}, {{ $studentId }})"
                                    class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                    Buka Kembali
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
