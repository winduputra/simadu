@php
    $previewType = $document->previewType();
@endphp

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4">
        <div class="min-w-0">
            <h2 class="font-bold text-slate-800 truncate">{{ $document->nama }}</h2>
            <p class="text-xs text-slate-400 truncate">{{ $document->nama_file_asli }} · {{ $document->formattedSize() }}</p>
        </div>
        @if($canDownload)
            <a href="{{ $downloadUrl }}" class="shrink-0 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-all">Download</a>
        @endif
    </div>

    <div class="bg-slate-100">
        @if($previewType === 'pdf')
            <iframe src="{{ $streamUrl }}" class="w-full h-[78vh] bg-white" title="Preview {{ $document->nama }}"></iframe>
        @elseif($previewType === 'image')
            <div class="min-h-[70vh] flex items-center justify-center p-4">
                <img src="{{ $streamUrl }}" alt="{{ $document->nama }}" class="max-h-[78vh] max-w-full rounded-xl shadow-sm bg-white">
            </div>
        @elseif($previewType === 'video')
            <div class="min-h-[70vh] flex items-center justify-center p-4 bg-slate-950">
                <video src="{{ $streamUrl }}" controls class="max-h-[78vh] max-w-full rounded-xl"></video>
            </div>
        @elseif($previewType === 'audio')
            <div class="min-h-[40vh] flex items-center justify-center p-8">
                <audio src="{{ $streamUrl }}" controls class="w-full max-w-xl"></audio>
            </div>
        @elseif($previewType === 'text')
            <iframe src="{{ $streamUrl }}" class="w-full h-[78vh] bg-white" title="Preview {{ $document->nama }}"></iframe>
        @else
            <div class="min-h-[45vh] flex items-center justify-center p-8 text-center">
                <div class="max-w-md space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-200 text-slate-500 flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Preview belum tersedia</h3>
                    <p class="text-sm text-slate-500">Browser belum bisa menampilkan file Word/Excel/PowerPoint langsung dari storage privat. Gunakan tombol download untuk membukanya di aplikasi terkait.</p>
                </div>
            </div>
        @endif
    </div>
</div>
