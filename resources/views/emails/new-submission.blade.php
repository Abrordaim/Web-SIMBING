@extends('emails.layouts.base')
@section('title', 'Pengajuan Baru Masuk')

@section('content')
<h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0 0 8px;">
    Ada Pengajuan Baru!
</h2>
<p style="color:#64748b;font-size:14px;line-height:1.8;margin:0 0 28px;">
    Halo <strong>{{ $lecturerUser->name }}</strong>, mahasiswa bimbingan Anda baru saja mengirimkan dokumen skripsi yang memerlukan review Anda.
</p>

{{-- Submission Detail Card --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:28px;">
    <tr>
        <td style="padding:16px 20px;border-bottom:1px solid #e2e8f0;background:linear-gradient(135deg,#dbeafe,#bfdbfe);border-radius:12px 12px 0 0;">
            <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#1d4ed8;margin:0;">Detail Pengajuan</p>
        </td>
    </tr>
    <tr>
        <td style="padding:20px;">
            <table class="responsive-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Mahasiswa</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $studentUser->name }}</p>
                    </td>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Jenis Dokumen</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $submission->type }}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Judul Dokumen</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $submission->title }}</p>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Tanggal Submit</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">
                            {{ $submission->submitted_at->translatedFormat('d F Y, H:i') }}
                        </p>
                    </td>
                    @if($submission->chapter)
                    <td width="50%" style="vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">BAB / Chapter</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $submission->chapter }}</p>
                    </td>
                    @endif
                </tr>
            </table>
        </td>
    </tr>
</table>

@if($submission->description)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a;margin-bottom:28px;">
    <tr>
        <td style="padding:16px 20px;">
            <p style="font-size:11px;color:#92400e;font-weight:600;margin:0 0 6px;">📝 Catatan dari Mahasiswa</p>
            <p style="font-size:13px;color:#78350f;margin:0;line-height:1.7;">{{ $submission->description }}</p>
        </td>
    </tr>
</table>
@endif

{{-- CTA --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center">
            <a href="{{ config('app.url') . '/student-detail/' . $submission->supervision->student->id }}"
               style="display:inline-block;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-size:14px;font-weight:600;padding:14px 36px;border-radius:10px;text-decoration:none;box-shadow:0 4px 6px -1px rgba(37,99,235,0.2);">
                Buka Halaman Mahasiswa &rarr;
            </a>
        </td>
    </tr>
</table>
@endsection
