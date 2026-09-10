<x-app-layout>
    <div class="space-y-8">
        <!-- Trash Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Trash</h1>
                <p class="text-slate-500 text-sm mt-1">Folders and files in trash are still counted towards your quota. Permanently delete to free up space.</p>
            </div>
            @if(!$folders->isEmpty() || !$documents->isEmpty())
                <form action="{{ route('trash.empty') }}" method="POST" onsubmit="return confirm('Are you sure you want to empty the Trash? All items will be permanently deleted!');">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-rose-700 sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Empty Trash
                    </button>
                </form>
            @endif
        </div>

        <!-- Folders Section -->
        @if(!$folders->isEmpty())
        <div>
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Deleted Folders</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach($folders as $f)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md xl:flex-col xl:items-stretch xl:gap-3 2xl:flex-row 2xl:items-center">
                        <div class="flex min-w-0 flex-1 items-center space-x-3 mr-2 xl:mr-0 2xl:mr-2">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <span class="min-w-0 break-words text-sm font-semibold text-slate-600" title="{{ $f->nama }}">
                                {{ $f->nama }}
                            </span>
                        </div>

                        <!-- Restore & Delete Actions -->
                        <div class="flex shrink-0 items-center space-x-1 xl:justify-end 2xl:justify-start">
                            <form action="{{ route('trash.folders.restore', $f->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Restore" aria-label="Restore {{ $f->nama }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('trash.folders.force-delete', $f->id) }}" method="POST" onsubmit="return confirm('Permanently delete folder and all contents? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-all" title="Permanently Delete" aria-label="Permanently delete {{ $f->nama }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Files Section -->
        <div>
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Deleted Files</h2>
            @if($documents->isEmpty() && $folders->isEmpty())
                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <p class="text-slate-500 font-medium">Trash is empty.</p>
                </div>
            @elseif(!$documents->isEmpty())
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div data-responsive-card-list class="divide-y divide-slate-100 lg:hidden">
                        @foreach($documents as $doc)
                            <article class="space-y-4 p-4 sm:p-5">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="break-words text-sm font-semibold text-slate-700">{{ $doc->nama }}</p>
                                        <p class="break-all text-xs text-slate-400">{{ $doc->nama_file_asli }}</p>
                                    </div>
                                </div>
                                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Size</dt>
                                        <dd class="mt-1 font-medium text-slate-600">{{ $doc->formattedSize() }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Deleted At</dt>
                                        <dd class="mt-1 text-slate-500">{{ $doc->deleted_at->diffForHumans() }}</dd>
                                    </div>
                                </dl>
                                <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-3">
                                    <form action="{{ route('trash.documents.restore', $doc->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-emerald-600 hover:bg-emerald-50" aria-label="Restore {{ $doc->nama }}">Restore</button>
                                    </form>
                                    <form action="{{ route('trash.documents.force-delete', $doc->id) }}" method="POST" onsubmit="return confirm('Permanently delete file? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-rose-600 hover:bg-rose-50" aria-label="Permanently delete {{ $doc->nama }}">Permanently Delete</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div data-responsive-desktop-table class="hidden lg:block">
                        <table class="w-full table-fixed text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                    <th class="px-2 py-3 xl:px-6">Name</th>
                                    <th class="px-2 py-3 xl:px-6">Size</th>
                                    <th class="px-2 py-3 xl:px-6">Deleted At</th>
                                    <th class="px-2 py-3 text-right xl:px-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($documents as $doc)
                                    <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                        <td class="px-2 py-4 font-semibold text-slate-800 xl:px-6">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div class="min-w-0 break-words">
                                                    <span class="text-slate-600">{{ $doc->nama }}</span>
                                                    <span class="block break-all text-[10px] text-slate-400">{{ $doc->nama_file_asli }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 py-4 font-medium text-slate-500 xl:px-6">
                                            {{ $doc->formattedSize() }}
                                        </td>
                                        <td class="px-2 py-4 text-slate-400 xl:px-6">
                                            {{ $doc->deleted_at->diffForHumans() }}
                                        </td>
                                        <td class="px-2 py-4 text-right xl:px-6">
                                            <div class="flex flex-wrap items-center justify-end gap-1 xl:gap-2">
                                                <form action="{{ route('trash.documents.restore', $doc->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Restore" aria-label="Restore {{ $doc->nama }}">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('trash.documents.force-delete', $doc->id) }}" method="POST" onsubmit="return confirm('Permanently delete file? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-all" title="Permanently Delete" aria-label="Permanently delete {{ $doc->nama }}">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
