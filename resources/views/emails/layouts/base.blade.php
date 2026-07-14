<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'SIMBING')</title>
    <style>
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .header-cell {
                padding: 24px 20px !important;
            }
            .body-cell {
                padding: 24px 20px !important;
            }
            .footer-cell {
                padding: 20px !important;
            }
            .responsive-table td {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .responsive-table td:first-child {
                padding-bottom: 16px !important;
            }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:'Segoe UI',Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;padding:32px 16px;">
    <tr><td align="center">
        <table class="email-container" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:580px;background:#ffffff;border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1),0 2px 4px -1px rgba(0,0,0,0.06);">

            {{-- HEADER --}}
            <tr>
                <td class="header-cell" style="background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);border-radius:20px 20px 0 0;padding:36px 40px;text-align:center;">
                    <img src="{{ $message->embed(public_path('simbing-logo.png')) }}" alt="SIMBING Logo" style="max-width:80px; margin-bottom:12px;"> 
                    <h1 style="color:#fff;font-size:22px;font-weight:700;margin:0;letter-spacing:-0.3px;">SIMBING</h1>
                    <p style="color:rgba(255,255,255,0.7);font-size:11px;margin:6px 0 0;letter-spacing:1px;text-transform:uppercase;">Sistem Manajemen Bimbingan Skripsi</p>
                </td>
            </tr>

            {{-- BODY --}}
            <tr>
                <td class="body-cell" style="background:#ffffff;padding:40px;">
                    @yield('content')
                </td>
            </tr>

            {{-- FOOTER --}}
            <tr>
                <td class="footer-cell" style="background:#f8fafc;border-radius:0 0 20px 20px;padding:24px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                    <p style="color:#94a3b8;font-size:12px;margin:0;line-height:1.8;">
                        © {{ date('Y') }} SIMBING — Universitas Siap Tak Gentar<br>
                        <span style="font-size:11px;color:#cbd5e1;">Email ini dikirim otomatis. Mohon tidak membalas email ini.</span>
                    </p>
                </td>
            </tr>

        </table>
    </td></tr>
</table>
</body>
</html>
