<x-app-layout>
    <div class="space-y-8">
        <!-- Trash Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Trash</h1>
                <p class="text-slate-500 text-sm mt-1">Folders and files in trash are still counted towards your quota. Permanently delete to free up space.</p>
            </div>
            @if(!$folders->isEmpty() || !$documents->isEmpty())
                <form action="{{ route('trash.empty') }}" method="POST" onsubmit="return confirm('Are you sure you want to empty the Trash? All items will be permanently deleted!');">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($folders as $f)
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                        <div class="flex items-center space-x-3 truncate flex-1 mr-2">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <span class="font-semibold text-slate-600 text-sm truncate">
                                {{ $f->nama }}
                            </span>
                        </div>

                        <!-- Restore & Delete Actions -->
                        <div class="flex items-center space-x-1 shrink-0">
                            <form action="{{ route('trash.folders.restore', $f->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Restore">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('trash.folders.force-delete', $f->id) }}" method="POST" onsubmit="return confirm('Permanently delete folder and all contents? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-all" title="Permanently Delete">
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
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-6">Name</th>
                                    <th class="py-3 px-6">Size</th>
                                    <th class="py-3 px-6">Deleted At</th>
                                    <th class="py-3 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($documents as $doc)
                                    <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                        <td class="py-4 px-6 font-semibold text-slate-800">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div class="truncate max-w-xs">
                                                    <span class="text-slate-600">{{ $doc->nama }}</span>
                                                    <span class="text-[10px] text-slate-400 block truncate">{{ $doc->nama_file_asli }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-slate-500 font-medium">
                                            {{ $doc->formattedSize() }}
                                        </td>
                                        <td class="py-4 px-6 text-slate-400">
                                            {{ $doc->deleted_at->diffForHumans() }}
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <form action="{{ route('trash.documents.restore', $doc->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Restore">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('trash.documents.force-delete', $doc->id) }}" method="POST" onsubmit="return confirm('Permanently delete file? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-all" title="Permanently Delete">
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
