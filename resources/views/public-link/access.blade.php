<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shared Item - SIMADU</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 flex items-center justify-center min-h-screen p-6">
        <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-xl p-8 shadow-xl text-center space-y-6">
            @php
                $item = $link->linkable;
                $isDoc = $item instanceof \App\Models\Document;
            @endphp

            <!-- Icon -->
            <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto shadow-sm">
                @if($isDoc)
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @else
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                @endif
            </div>

            <div>
                <h1 class="text-xl font-bold text-slate-800 truncate max-w-full" title="{{ $item->nama }}">
                    {{ $item->nama }}
                </h1>
                <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Shared with you</p>
            </div>

            <!-- Details Block -->
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-left grid grid-cols-2 gap-4 text-xs">
                @if($isDoc)
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase tracking-wider">File Size</span>
                        <span class="text-slate-700 font-medium mt-0.5 block">{{ $item->formattedSize() }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase tracking-wider">File Type</span>
                        <span class="text-slate-700 font-medium mt-0.5 block truncate" title="{{ $item->mime_type }}">{{ $item->mime_type }}</span>
                    </div>
                @endif
                <div>
                    <span class="text-slate-400 font-semibold block uppercase tracking-wider">Shared By</span>
                    <span class="text-slate-700 font-medium mt-0.5 block">{{ $link->user?->nama }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block uppercase tracking-wider">Permission</span>
                    <span class="text-slate-700 font-medium mt-0.5 block capitalize">{{ $link->permission }}</span>
                </div>
            </div>

            <!-- Actions block -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                @if($isDoc && $link->permission === 'editor')
                    <a href="{{ route('public.download', $link->token) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download File
                    </a>
                @endif
            </div>

            <div class="text-[10px] text-slate-400">
                Powered by SIMADU Document System
            </div>
        </div>
    </body>
</html>
