<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Password Protected Link - SIMADU</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 flex items-center justify-center min-h-screen p-6">
        <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-8 shadow-xl text-center space-y-6">
            <!-- Icon -->
            <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto shadow-sm">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>

            <div>
                <h1 class="text-xl font-bold text-slate-800">Password Protected Link</h1>
                <p class="text-slate-500 text-sm mt-1">This link requires a password to access.</p>
            </div>

            @if (session('error'))
                <div class="p-3.5 bg-rose-50 border border-rose-100 text-rose-800 text-xs font-semibold rounded-xl text-left">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('public.verify', $token) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <input type="password" name="password" required placeholder="Enter password..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-center">
                </div>
                <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                    Access File
                </button>
            </form>
        </div>
    </body>
</html>
