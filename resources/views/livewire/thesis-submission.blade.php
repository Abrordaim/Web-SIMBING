<div class="p-4 h-full sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Pengajuan Bimbingan</h1>
        <p class="text-gray-600 text-sm sm:text-base">Kirim dokumen skripsi untuk mendapatkan feedback dari dosen pembimbing</p>
    </div>

    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Submission Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Form Pengajuan Baru</h2>

                <form wire:submit="submit" class="space-y-6">
                    {{-- Dosen Pembimbing Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dosen Pembimbing <span class="text-red-500">*</span>
                        </label>
                        @if(count($supervisorOptions) > 0)
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                <select wire:model="selectedSupervisionId" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none appearance-none bg-white" required>
                                    <option value="">Pilih Dosen Pembimbing</option>
                                    @foreach($supervisorOptions as $option)
                                        <option value="{{ $option['id'] }}">{{ $option['name'] }} — {{ $option['department'] }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                            </div>
                        @else
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center gap-2 text-yellow-700 text-sm">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                    <span>Anda belum memilih dosen pembimbing. Silakan atur di <a href="{{ route('profile') }}" class="text-blue-600 hover:underline font-medium" wire:navigate>halaman Profil</a> terlebih dahulu.</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Bimbingan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" placeholder="Contoh: Bimbingan BAB 4 - Implementasi Sistem" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bab/Bagian <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="chapter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
                            <option value="">Pilih Bab</option>
                            <option value="BAB 1">BAB 1 - Pendahuluan</option>
                            <option value="BAB 2">BAB 2 - Tinjauan Pustaka</option>
                            <option value="BAB 3">BAB 3 - Metodologi Penelitian</option>
                            <option value="BAB 4">BAB 4 - Implementasi Sistem</option>
                            <option value="BAB 5">BAB 5 - Hasil dan Pembahasan</option>
                            <option value="BAB 6">BAB 6 - Kesimpulan dan Saran</option>
                            <option value="PROPOSAL">Proposal</option>
                            <option value="FULL">Draft Lengkap</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi/Catatan</label>
                        <textarea wire:model="description" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none" rows="4" placeholder="Tambahkan catatan atau hal yang ingin didiskusikan dengan dosen pembimbing..."></textarea>
                    </div>

                    {{-- Jadwal Konsultasi (Wajib) --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            <h3 class="font-medium text-gray-900">Jadwal Konsultasi <span class="text-red-500">*</span></h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Konsultasi <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                                    <input type="date" wire:model="meetingDate" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    <input type="time" wire:model="meetingTime" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Konsultasi <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center gap-2 p-3 border-2 rounded-lg cursor-pointer transition-colors {{ $meetingType === 'offline' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }}">
                                    <input type="radio" wire:model.live="meetingType" value="offline" class="sr-only">
                                    <span class="text-sm font-medium">Offline (Ruang Dosen)</span>
                                </label>
                                <label class="flex items-center justify-center gap-2 p-3 border-2 rounded-lg cursor-pointer transition-colors {{ $meetingType === 'online' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }}">
                                    <input type="radio" wire:model.live="meetingType" value="online" class="sr-only">
                                    <span class="text-sm font-medium">Online (Google Meet)</span>
                                </label>
                            </div>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-xs text-blue-700"><strong>Catatan:</strong> Jadwal konsultasi ini masih bersifat pengajuan dan menunggu konfirmasi dari dosen pembimbing.</p>
                        </div>
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen <span class="text-red-500">*</span></label>
                        @if(!$selectedFile)
                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                                    <p class="mb-2 text-sm text-gray-600"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX (Maks. 10MB)</p>
                                </div>
                                <input type="file" wire:model="selectedFile" class="hidden" accept=".pdf,.doc,.docx">
                            </label>
                        @else
                            <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $selectedFile->getClientOriginalName() }}</p>
                                        <p class="text-xs text-gray-600">{{ number_format($selectedFile->getSize() / 1024 / 1024, 2) }} MB</p>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeFile" class="p-2 hover:bg-blue-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2" {{ count($supervisorOptions) === 0 ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                            Kirim Pengajuan
                        </button>
                        <button type="button" wire:click="$refresh" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Previous Submissions --}}
        <div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Riwayat Pengajuan</h2>
                <div class="space-y-4">
                    @foreach($previousSubmissions as $submission)
                        @php
                            $statusColors = [
                                'green' => 'bg-green-50 text-green-700 border-green-200',
                                'yellow' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'red' => 'bg-red-50 text-red-700 border-red-200',
                            ];
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">{{ $submission['chapter'] }}</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium border {{ $statusColors[$submission['statusColor']] ?? '' }}">{{ $submission['status'] }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 text-sm mb-1">{{ $submission['title'] }}</h3>
                            <p class="text-xs text-gray-500 mb-1">{{ $submission['date'] }}</p>
                            <p class="text-xs text-blue-600">Dosen: {{ $submission['lecturer'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-blue-50 rounded-xl p-6 border border-blue-200 mt-6">
                <h3 class="font-bold text-gray-900 mb-2">Tips Pengajuan</h3>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li>• Pastikan dokumen dalam format PDF atau Word</li>
                    <li>• Beri nama file dengan jelas (misal: BAB3_NamaAnda.pdf)</li>
                    <li>• Sertakan catatan spesifik untuk dosen</li>
                    <li>• Review dokumen sebelum mengirim</li>
                </ul>
            </div>
        </div>
    </div>
</div>
