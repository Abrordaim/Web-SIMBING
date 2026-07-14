<div class="flex-1 flex flex-col bg-gray-100 overflow-hidden">
    @if($selectedPdfUrl && $selectedThread)
        {{-- PDF Title Bar --}}
        {{-- <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200 px-5 py-2.5 flex items-center gap-3 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1.25v3.25H11V13h1.25v-1H8.5v1zm5 0h1.5v2.25L16.5 13h1v4h-1v-2.25L15 17h-1.5v-4z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ $selectedThread['docTitle'] }}</p>
                <p class="text-xs text-gray-400">{{ $selectedThread['docDate'] }} · {{ $selectedThread['docType'] }}</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                @php $cfg = $statusCfg[$selectedThread['docStatus']] ?? $statusCfg['pending']; @endphp
                <span class="flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full border {{ $cfg['cls'] }}">
                    {{ $cfg['label'] }}
                </span>
                <a href="{{ $selectedPdfUrl }}" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                    Buka
                </a>
            </div>
        </div> --}}

        {{-- PDF iframe --}}
        <div class="flex-1 p-2">
            <div class="w-full h-full rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-white">
                <iframe
                    src="{{ $selectedPdfUrl }}#toolbar=1&navpanes=0"
                    class="w-full h-full"
                    style="min-height: 400px"
                    frameborder="0"
                ></iframe>
            </div>
        </div>
    @else
        {{-- Empty state --}}
        <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
            <div class="w-20 h-20 rounded-2xl bg-gray-200/50 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Pilih bab untuk melihat PDF</p>
            <p class="text-xs text-gray-400 mt-1">Klik salah satu bab di sidebar kanan</p>
        </div>
    @endif
</div>
