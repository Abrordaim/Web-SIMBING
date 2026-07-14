@extends('emails.layouts.base')
@section('title', 'Selamat Datang di SIMBING')

@section('content')
{{-- Greeting --}}
<h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0 0 8px;">
    Selamat Datang, {{ $user->name }}! 🎉
</h2>
<p style="color:#64748b;font-size:14px;line-height:1.8;margin:0 0 28px;">
    Akun Anda di SIMBING telah berhasil dibuat. Anda kini dapat mengakses semua fitur manajemen bimbingan skripsi.
</p>

{{-- Info Card --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:28px;">
    <tr>
        <td style="padding:16px 20px;border-bottom:1px solid #e2e8f0;">
            <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin:0;">Informasi Akun</p>
        </td>
    </tr>
    <tr>
        <td style="padding:20px;">
            <table class="responsive-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Nama Lengkap</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $user->name }}</p>
                    </td>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Email</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $user->email }}</p>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Role</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">
                            {{ $user->role === 'student' ? ' Mahasiswa' : 'Dosen' }}
                        </p>
                    </td>
                    <td width="50%" style="vertical-align:top;">
                        @if($user->isStudent() && $user->student?->nim)
                            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">NIM</p>
                            <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $user->student->nim }}</p>
                        @elseif($user->isLecturer() && $user->lecturer?->nidn)
                            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">NIDN</p>
                            <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $user->lecturer->nidn }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- Tips --}}
<p style="font-size:13px;font-weight:600;color:#334155;margin:0 0 12px;">
    {{ $user->isStudent() ? 'Langkah selanjutnya:' : 'Yang bisa Anda lakukan:' }}
</p>
@if($user->isStudent())
    @foreach(['📤 Submit pengajuan bab pertama skripsi Anda', '📅 Jadwalkan sesi bimbingan perdana', '💬 Lihat dan balas feedback dosen di halaman Revisi'] as $tip)
        <p style="font-size:13px;color:#475569;margin:0 0 8px;padding-left:4px;">{{ $tip }}</p>
    @endforeach
@else
    @foreach(['📋 Pantau daftar mahasiswa bimbingan di Dashboard', '✅ Review dan beri keputusan pengajuan yang masuk', '📅 Konfirmasi jadwal bimbingan dari mahasiswa'] as $tip)
        <p style="font-size:13px;color:#475569;margin:0 0 8px;padding-left:4px;">{{ $tip }}</p>
    @endforeach
@endif

{{-- CTA --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:32px;">
    <tr>
        <td align="center">
            <a href="{{ config('app.url') }}"
               style="display:inline-block;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-size:14px;font-weight:600;padding:14px 36px;border-radius:10px;text-decoration:none;letter-spacing:0.2px;box-shadow:0 4px 6px -1px rgba(37,99,235,0.2);">
                Masuk ke Dashboard &rarr;
            </a>
        </td>
    </tr>
</table>
@endsection
