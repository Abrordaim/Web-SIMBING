<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Lengkapi Profil Anda</h1>
            <p class="text-gray-500 mt-1 text-sm">SIMBING - Sistem Manajemen Bimbingan Skripsi</p>
        </div>

        {{-- Step Indicator --}}
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center gap-3">
                {{-- Step 1 --}}
                <div class="flex items-center gap-2">
                    <div @class([
                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300',
                        'bg-blue-600 text-white shadow-md shadow-blue-200' => $step === 1,
                        'bg-green-500 text-white' => $step > 1,
                    ])>
                        @if ($step > 1)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        @else
                            1
                        @endif
                    </div>
                    <span @class([
                        'text-sm font-medium transition-colors',
                        'text-blue-600' => $step === 1,
                        'text-green-600' => $step > 1,
                    ])>Pilih Peran</span>
                </div>

                {{-- Connector --}}
                <div @class([
                    'w-12 h-0.5 rounded-full transition-colors duration-500',
                    'bg-green-400' => $step > 1,
                    'bg-gray-200' => $step === 1,
                ])></div>

                {{-- Step 2 --}}
                <div class="flex items-center gap-2">
                    <div @class([
                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300',
                        'bg-blue-600 text-white shadow-md shadow-blue-200' => $step === 2,
                        'bg-gray-200 text-gray-400' => $step < 2,
                    ])>2</div>
                    <span @class([
                        'text-sm font-medium transition-colors',
                        'text-blue-600' => $step === 2,
                        'text-gray-400' => $step < 2,
                    ])>Info Profil</span>
                </div>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">

            {{-- ============ STEP 1: Role Selection ============ --}}
            @if ($step === 1)
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Anda terdaftar sebagai?</h2>
                    <p class="text-sm text-gray-500 mb-6">Pilih peran yang sesuai untuk mengatur tampilan dan akses Anda.</p>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        {{-- Card: Mahasiswa --}}
                        <button
                            wire:click="selectRole('student')"
                            type="button"
                            @class([
                                'relative flex flex-col items-center gap-3 p-6 rounded-xl border-2 transition-all duration-200 text-left hover:shadow-md cursor-pointer',
                                'border-blue-500 bg-blue-50 shadow-md shadow-blue-100' => $role === 'student',
                                'border-gray-200 hover:border-blue-300 hover:bg-gray-50' => $role !== 'student',
                            ])
                        >
                            @if ($role === 'student')
                                <div class="absolute top-3 right-3 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                            @endif
                            <div @class([
                                'w-14 h-14 rounded-xl flex items-center justify-center',
                                'bg-blue-100' => $role === 'student',
                                'bg-gray-100' => $role !== 'student',
                            ])>
                                <svg @class([
                                    'w-7 h-7',
                                    'text-blue-600' => $role === 'student',
                                    'text-gray-500' => $role !== 'student',
                                ]) fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                            </div>
                            <div class="text-center">
                                <p @class([
                                    'font-semibold text-sm',
                                    'text-blue-700' => $role === 'student',
                                    'text-gray-700' => $role !== 'student',
                                ])>Mahasiswa</p>
                                <p class="text-xs text-gray-400 mt-0.5">Bimbingan skripsi</p>
                            </div>
                        </button>

                        {{-- Card: Dosen --}}
                        <button
                            wire:click="selectRole('lecturer')"
                            type="button"
                            @class([
                                'relative flex flex-col items-center gap-3 p-6 rounded-xl border-2 transition-all duration-200 text-left hover:shadow-md cursor-pointer',
                                'border-blue-500 bg-blue-50 shadow-md shadow-blue-100' => $role === 'lecturer',
                                'border-gray-200 hover:border-blue-300 hover:bg-gray-50' => $role !== 'lecturer',
                            ])
                        >
                            @if ($role === 'lecturer')
                                <div class="absolute top-3 right-3 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                            @endif
                            <div @class([
                                'w-14 h-14 rounded-xl flex items-center justify-center',
                                'bg-blue-100' => $role === 'lecturer',
                                'bg-gray-100' => $role !== 'lecturer',
                            ])>
                                <svg @class([
                                    'w-7 h-7',
                                    'text-blue-600' => $role === 'lecturer',
                                    'text-gray-500' => $role !== 'lecturer',
                                ]) fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                </svg>
                            </div>
                            <div class="text-center">
                                <p @class([
                                    'font-semibold text-sm',
                                    'text-blue-700' => $role === 'lecturer',
                                    'text-gray-700' => $role !== 'lecturer',
                                ])>Dosen</p>
                                <p class="text-xs text-gray-400 mt-0.5">Pembimbing skripsi</p>
                            </div>
                        </button>
                    </div>

                    <button
                        wire:click="nextStep"
                        type="button"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-100 flex items-center justify-center gap-2"
                    >
                        Lanjutkan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- ============ STEP 2: Profile Info ============ --}}
            @if ($step === 2)
                <form wire:submit="complete">
                    <div class="flex items-center gap-3 mb-6">
                        <div @class([
                            'w-10 h-10 rounded-xl flex items-center justify-center',
                            'bg-blue-100' => $role === 'student',
                            'bg-purple-100' => $role === 'lecturer',
                        ])>
                            @if ($role === 'student')
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">
                                {{ $role === 'student' ? 'Data Mahasiswa' : 'Data Dosen' }}
                            </h2>
                            <p class="text-xs text-gray-400">Semua field bersifat opsional dan bisa diubah nanti</p>
                        </div>
                    </div>

                    <div class="space-y-4">

                        @if ($role === 'student')
                            {{-- NIM --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    NIM <span class="text-gray-400 font-normal">(Nomor Induk Mahasiswa)</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="nim"
                                    placeholder="Contoh: 21101234"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-shadow text-sm"
                                >
                                @error('nim')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Semester --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Semester</label>
                                <select
                                    wire:model="semester"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-shadow text-sm bg-white"
                                >
                                    @for ($i = 1; $i <= 14; $i++)
                                        <option value="{{ $i }}">Semester {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        @else
                            {{-- NIDN --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    NIDN <span class="text-gray-400 font-normal">(Nomor Induk Dosen Nasional)</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="nidn"
                                    placeholder="Contoh: 0012345678"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-shadow text-sm"
                                >
                                @error('nidn')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Spesialisasi --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bidang Keahlian / Spesialisasi</label>
                                <input
                                    type="text"
                                    wire:model="specialization"
                                    placeholder="Contoh: Sistem Informasi, Machine Learning"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-shadow text-sm"
                                >
                            </div>
                        @endif

                        {{-- Departemen --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Studi / Departemen</label>
                            <input
                                type="text"
                                wire:model="department"
                                placeholder="Contoh: Teknik Informatika"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-shadow text-sm"
                            >
                        </div>

                        {{-- Fakultas --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fakultas</label>
                            <input
                                type="text"
                                wire:model="faculty"
                                placeholder="Contoh: Fakultas Teknik"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-shadow text-sm"
                            >
                        </div>

                        {{-- Info note --}}
                        <div class="flex items-start gap-2 p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                            <p class="text-xs text-blue-700">
                                Semua field di sini bersifat opsional. Anda bisa melengkapi atau mengubahnya kapan saja melalui halaman <strong>Profil</strong>.
                            </p>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 mt-6">
                        <button
                            type="button"
                            wire:click="prevStep"
                            class="flex-1 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-medium hover:bg-gray-50 hover:border-gray-300 transition-all text-sm flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Kembali
                        </button>
                        <button
                            type="submit"
                            class="flex-[2] bg-blue-600 text-white py-3 rounded-xl font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-100 flex items-center justify-center gap-2 text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Selesai 
                        </button>
                    </div>
                </form>
            @endif

        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            © 2026 Universitas XYZ. All rights reserved.
        </p>
    </div>
</div>
