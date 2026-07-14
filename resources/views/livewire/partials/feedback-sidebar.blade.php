<aside class="w-full md:w-64 lg:w-72 flex-shrink-0 bg-white border-t md:border-t-0 md:border-l border-gray-200 flex flex-col overflow-hidden h-full">
    {{-- Sidebar Header --}}
    <div class="px-5 py-4 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center gap-2.5 mb-3">
            {{-- <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
            </div> --}}
            {{-- <div>
                <h3 class="font-bold text-gray-900 text-sm">Feedback & Revisi</h3>
                <p class="text-xs text-gray-400">{{ count($threads) }} dokumen</p>
            </div> --}}
        </div>

        {{-- Filter --}}
        <div class="flex items-center gap-1">
            @foreach(['all' => 'Semua', 'open' => 'Terbuka', 'resolved' => 'Selesai'] as $key => $label)
                <button
                    wire:click="$set('filter', '{{ $key }}')"
                    class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors {{ $this->filter === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Bab List + Feedback Threads --}}
    <div class="flex-1 overflow-y-auto">
        @if(count($threads) === 0)
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-10 h-10 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                <p class="text-sm">Tidak ada thread untuk filter ini</p>
            </div>
        @endif

        @foreach($threads as $thread)
            @php
                $cfg = $statusCfg[$thread['docStatus']] ?? $statusCfg['pending'];
                $isSelectedBab = $this->selectedBabId === $thread['id'];
                $isExpanded = in_array($thread['id'], $this->expandedThreads);
            @endphp

            <div class="border-b border-gray-100">
                {{-- Bab Item Button --}}
                <div
                    wire:click="selectBab({{ $thread['id'] }})"
                    class="w-full px-4 py-3 cursor-pointer text-left transition-all {{ $isSelectedBab ? 'bg-blue-50 border-l-[3px] border-l-blue-600' : 'hover:bg-gray-50 border-l-[3px] border-l-transparent' }}"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 {{ $isSelectedBab ? 'bg-blue-600 text-white' : ($thread['resolved'] ? 'bg-gray-100 text-gray-400' : 'bg-blue-50 text-blue-600') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-semibold truncate {{ $isSelectedBab ? 'text-blue-700' : ($thread['resolved'] ? 'text-gray-500' : 'text-gray-900') }}">{{ $thread['docTitle'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full border {{ $cfg['cls'] }}">{{ $cfg['label'] }}</span>
                                <span class="text-xs text-gray-400">{{ $thread['docDate'] }}</span>
                                @if($thread['resolved'])
                                    <span class="flex items-center gap-0.5 text-xs text-green-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                        Selesai
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                                {{ count($thread['comments']) }} komentar
                            </p>
                        </div>
                        {{-- Expand/Collapse Chat Toggle --}}
                        <button
                            wire:click.stop="toggleThread({{ $thread['id'] }})"
                            class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors flex-shrink-0 {{ $isExpanded ? 'bg-blue-100 text-blue-600' : 'text-gray-400' }}"
                            title="{{ $isExpanded ? 'Tutup chat' : 'Buka chat' }}"
                        >
                            <svg class="w-4 h-4 transition-transform {{ $isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </button>
                    </div>
                </div>

                {{-- Expandable Chat/Comments Section --}}
                @if($isExpanded)
                    <div class="bg-gray-50 border-t border-gray-100">
                        {{-- Comments --}}
                        <div class="px-4 py-3 space-y-0">
                            @foreach($thread['comments'] as $idx => $comment)
                                @php
                                    $isFromLecturer = $comment['author'] === 'lecturer';
                                    $isLast = $idx === count($thread['comments']) - 1;
                                @endphp
                                <div class="flex gap-2.5 group">
                                    <div class="flex flex-col items-center">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center font-semibold text-[10px] flex-shrink-0 {{ $isFromLecturer ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                            @if($comment['avatar'])
                                                <img src="{{ $comment['avatar'] }}" alt="{{ $comment['name'] }}" class="w-full h-full object-cover rounded-full">
                                            @else
                                                {{ strtoupper(substr($comment['name'], 0, 2)) }}
                                            @endif
                                        </div>
                                        @if(!$isLast)
                                            <div class="w-0.5 bg-gray-200 flex-1 mt-1 mb-1" style="min-height: 12px"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 mb-2 {{ $isLast ? 'mb-1' : '' }}">
                                        <div class="rounded-xl rounded-tl-sm px-3 py-2 {{ $isFromLecturer ? 'border border-blue-600  text-gray-900' : 'bg-white text-gray-900 border border-gray-200' }}">
                                            <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                                <span class="text-[11px] font-semibold {{ $isFromLecturer ? 'text-gray-900' : 'text-gray-900' }}">
                                                    {{ $comment['name'] }}
                                                    @if($isFromLecturer)
                                                        <span class="ml-1 bg-white/20 px-1 py-0.5 rounded text-white text-[9px]">Dosen</span>
                                                    @endif
                                                </span>
                                                <span class="text-[10px] {{ $isFromLecturer ? 'text-gray-400' : 'text-gray-400' }}">{{ $comment['time'] }}</span>
                                            </div>
                                            <p class="text-xs leading-relaxed">{{ $comment['text'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Reply / Resolve --}}
                        @if(!$thread['resolved'])
                            <div class="px-4 pb-3">
                                <div class="flex gap-2 items-end">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center font-semibold text-[10px] flex-shrink-0 {{ $isLecturer ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $isLecturer ? 'AS' : 'SW' }}
                                    </div>
                                    <div class="flex-1 border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-400 focus-within:border-transparent transition-all bg-white">
                                        <textarea
                                            rows="2"
                                            wire:model="replyTexts.{{ $thread['id'] }}"
                                            placeholder="Balas komentar..."
                                            class="w-full px-3 pt-2 pb-1 text-xs resize-none outline-none text-gray-800 placeholder-gray-400"
                                        ></textarea>
                                        <div class="flex items-center justify-end px-2 pb-2 pt-0.5">
                                            <div class="flex items-center gap-1.5">
                                                @if($isLecturer)
                                                    <button
                                                        wire:click="resolveThread({{ $thread['id'] }}, {{ $studentId }})"
                                                        class="flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors"
                                                    >
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                                        Selesai
                                                    </button>
                                                @endif
                                                <button
                                                    wire:click="sendReply({{ $thread['id'] }}, {{ $studentId }})"
                                                    class="flex items-center gap-1 px-2.5 py-1 bg-blue-600 text-white text-[11px] font-medium rounded-lg hover:bg-blue-700 transition-colors"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                                                    Balas
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="px-4 pb-3 flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-green-600 text-xs font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    Sudah selesai
                                </div>
                                @if($isLecturer)
                                    <button
                                        wire:click="resolveThread({{ $thread['id'] }}, {{ $studentId }})"
                                        class="flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                        Buka Kembali
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</aside>
