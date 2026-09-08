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
    <body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen p-6">
        <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-5xl mx-auto p-8 shadow-xl text-center space-y-6">
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

            <!-- Actions / Contents block -->
            @if($isDoc)
                @include('components.document-viewer', [
                    'document' => $item,
                    'streamUrl' => route('public.stream', $link->token),
                    'downloadUrl' => route('public.download', $link->token),
                    'canDownload' => $link->permission === 'editor',
                ])

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if($link->permission === 'editor')
                        <a href="{{ route('public.download', $link->token) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download File
                        </a>
                    @endif
                </div>
            @else
                <div class="text-left space-y-4">
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Isi Folder</h3>
                        @php
                            $subfolders = \App\Models\Folder::where('parent_id', $item->id)->orderBy('nama')->get();
                            $subdocs = \App\Models\Document::where('folder_id', $item->id)->orderBy('nama')->get();
                        @endphp

                        @if($subfolders->isEmpty() && $subdocs->isEmpty())
                            <p class="text-slate-400 text-xs text-center py-4">Folder ini kosong.</p>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach($subfolders as $sf)
                                    <div class="py-2.5 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate">
                                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                            <span class="font-medium text-slate-700 text-xs truncate">{{ $sf->nama }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 uppercase font-medium">Folder</span>
                                    </div>
                                @endforeach
                                @foreach($subdocs as $sd)
                                    <div class="py-2.5 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate max-w-[65%]">
                                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span class="font-medium text-slate-700 text-xs truncate" title="{{ $sd->nama_file_asli }}">{{ $sd->nama_file_asli }}</span>
                                        </div>
                                        <div class="flex items-center space-x-3 shrink-0">
                                            <span class="text-[10px] text-slate-400">{{ $sd->formattedSize() }}</span>
                                            @if($sd->isPreviewable())
                                                <a href="{{ route('public.stream-file', ['token' => $link->token, 'document' => $sd->id]) }}" target="_blank" class="text-slate-600 hover:text-indigo-800 text-xs font-semibold">Preview</a>
                                            @endif
                                            @if($link->permission === 'editor')
                                                <a href="{{ route('public.download-file', ['token' => $link->token, 'document' => $sd->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Download</a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="text-[10px] text-slate-400">
                Powered by SIMADU Document System
            </div>
        </div>
    </body>
</html>
