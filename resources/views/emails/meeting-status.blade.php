@extends('emails.layouts.base')
@section('title', 'Update Jadwal Bimbingan')

@section('content')
@php
$isConfirmed = $status === 'confirmed';
$emoji   = $isConfirmed ? '✅' : '❌';
$label   = $isConfirmed ? 'Dikonfirmasi' : 'Ditolak';
$bg      = $isConfirmed ? '#f0fdf4' : '#fef2f2';
$border  = $isConfirmed ? '#86efac' : '#fca5a5';
$titleC  = $isConfirmed ? '#166534' : '#991b1b';
@endphp

<h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0 0 8px;">
    Halo, {{ $studentUser->name }} 👋
</h2>
<p style="color:#64748b;font-size:14px;line-height:1.8;margin:0 0 24px;">
    Ada update mengenai jadwal bimbingan Anda dengan <strong>{{ $lecturerUser->name }}</strong>.
</p>

{{-- Status Banner --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:{{ $bg }};border:2px solid {{ $border }};border-radius:14px;margin-bottom:24px;">
    <tr>
        <td style="padding:20px 24px;text-align:center;">
            <p style="font-size:36px;margin:0 0 8px;">{{ $emoji }}</p>
            <p style="font-size:16px;font-weight:700;color:{{ $titleC }};margin:0;">
                Jadwal {{ $label }}
            </p>
        </td>
    </tr>
</table>

{{-- Alasan Penolakan (hanya jika ditolak dan ada alasan) --}}
@if(!$isConfirmed && !empty($rejectReason))
<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:#fff7ed;border:2px solid #fed7aa;border-radius:12px;margin-bottom:24px;">
    <tr>
        <td style="padding:16px 20px;border-bottom:1px solid #fed7aa;">
            <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#c2410c;margin:0;">
                💬 Alasan Penolakan dari Dosen
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding:16px 20px;">
            <p style="font-size:14px;color:#7c2d12;line-height:1.7;margin:0;font-style:italic;">
                "{{ $rejectReason }}"
            </p>
        </td>
    </tr>
</table>
@endif

{{-- Meeting Detail --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:28px;">
    <tr>
        <td style="padding:16px 20px;border-bottom:1px solid #e2e8f0;">
            <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin:0;">Detail Jadwal Bimbingan</p>
        </td>
    </tr>
    <tr>
        <td style="padding:20px;">
            <table class="responsive-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Judul</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $meeting->title }}</p>
                    </td>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Tipe</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;text-transform:capitalize;">{{ $meeting->type }}</p>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">📅 Tanggal</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">
                            {{ $meeting->date->translatedFormat('d F Y') }}
                        </p>
                    </td>
                    <td width="50%" style="padding-bottom:14px;vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">⏰ Waktu</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">
                            {{ \Carbon\Carbon::parse($meeting->time_start)->format('H:i') }} WIB
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="vertical-align:top;">
                        <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">📍 Lokasi</p>
                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $meeting->location }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- CTA --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center">
            <a href="{{ config('app.url') . '/schedule' }}"
               style="display:inline-block;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-size:14px;font-weight:600;padding:14px 36px;border-radius:10px;text-decoration:none;box-shadow:0 4px 6px -1px rgba(37,99,235,0.2);">
                Lihat Jadwal Bimbingan &rarr;
            </a>
        </td>
    </tr>
</table>
@endsection
