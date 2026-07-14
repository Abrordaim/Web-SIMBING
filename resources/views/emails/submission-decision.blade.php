@extends('emails.layouts.base')
@section('title', 'Update Status Pengajuan Skripsi')

@section('content')
@php
$config = [
    'approved'       => ['emoji'=>'✅','label'=>'Disetujui','bg'=>'#f0fdf4','border'=>'#86efac','title'=>'#166534','text'=>'#15803d','badge_bg'=>'#dcfce7','badge_text'=>'#166534'],
    'revision_minor' => ['emoji'=>'🔄','label'=>'Revisi Minor','bg'=>'#fefce8','border'=>'#fde047','title'=>'#854d0e','text'=>'#a16207','badge_bg'=>'#fef9c3','badge_text'=>'#854d0e'],
    'revision_major' => ['emoji'=>'⚠️','label'=>'Revisi Mayor','bg'=>'#fff7ed','border'=>'#fb923c','title'=>'#9a3412','text'=>'#c2410c','badge_bg'=>'#ffedd5','badge_text'=>'#9a3412'],
    'rejected'       => ['emoji'=>'❌','label'=>'Tidak Disetujui','bg'=>'#fef2f2','border'=>'#fca5a5','title'=>'#991b1b','text'=>'#dc2626','badge_bg'=>'#fee2e2','badge_text'=>'#991b1b'],
];
$c = $config[$decisionType] ?? $config['revision_minor'];
@endphp

<h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0 0 8px;">
    Halo, {{ $studentUser->name }}
</h2>
<p style="color:#64748b;font-size:14px;line-height:1.8;margin:0 0 24px;">
    Dosen pembimbing Anda telah memberikan keputusan atas pengajuan skripsi yang Anda kirimkan.
</p>

{{-- Status Banner --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:{{ $c['bg'] }};border:2px solid {{ $c['border'] }};border-radius:14px;margin-bottom:24px;">
    <tr>
        <td style="padding:20px 24px;text-align:center;">
            <p style="font-size:36px;margin:0 0 8px;">{{ $c['emoji'] }}</p>
            <p style="font-size:16px;font-weight:700;color:{{ $c['title'] }};margin:0 0 4px;">Status: {{ $c['label'] }}</p>
            <p style="font-size:12px;color:{{ $c['text'] }};margin:0;">Diputuskan oleh {{ $lecturerUser->name }}</p>
        </td>
    </tr>
</table>

{{-- Submission Detail --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px;">
    <tr>
        <td style="padding:16px 20px;border-bottom:1px solid #e2e8f0;">
            <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin:0;">Detail Dokumen</p>
        </td>
    </tr>
    <tr>
        <td style="padding:20px;">
            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Judul Dokumen</p>
            <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0 0 14px;">{{ $submission->title }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Jenis</p>
            <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $submission->type }}{{ $submission->chapter ? ' — ' . $submission->chapter : '' }}</p>
        </td>
    </tr>
</table>

{{-- Feedback --}}
@if($feedbackText)
<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:{{ $c['bg'] }};border-radius:12px;border:1px solid {{ $c['border'] }};margin-bottom:28px;">
    <tr>
        <td style="padding:20px 24px;">
            <p style="font-size:12px;font-weight:600;color:{{ $c['title'] }};margin:0 0 8px;">💬 Catatan dari Dosen Pembimbing</p>
            <p style="font-size:14px;color:{{ $c['text'] }};margin:0;line-height:1.8;font-style:italic;">"{{ $feedbackText }}"</p>
        </td>
    </tr>
</table>
@else
<p style="font-size:13px;color:#94a3b8;margin:0 0 28px;font-style:italic;">Tidak ada catatan tambahan dari dosen pembimbing.</p>
@endif

{{-- CTA --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center">
            <a href="{{ config('app.url') . '/revision' }}"
               style="display:inline-block;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-size:14px;font-weight:600;padding:14px 36px;border-radius:10px;text-decoration:none;box-shadow:0 4px 6px -1px rgba(37,99,235,0.2);">
                Lihat Detail Feedback &rarr;
            </a>
        </td>
    </tr>
</table>
@endsection
