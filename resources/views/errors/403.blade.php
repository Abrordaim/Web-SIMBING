<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Akses Ditolak | SIMBING</title>
    <link rel="icon" type="image/png" href="{{ asset('simbing-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body> 
    <main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
        <div class="text-center">
            <img src="{{ asset('simbing-logo.png') }}" alt="403 Error" class="w-32 h-32 mx-auto mb-6">
            <p class="text-8xl font-semibold text-blue-600">403</p>
            <h1 class="mt-4 text-4xl font-semibold tracking-tight text-balance text-gray-900 sm:text-5xl">Akses Ditolak</h1>
            <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="/" class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Go back home</a>
           
            </div>
        </div>
    </main>

   
</body>
</html>
