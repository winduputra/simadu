<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Shared with me</h1>
            <p class="text-slate-500 text-sm mt-1">Files and folders shared with you or your unit.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @if($shares->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-slate-500 font-medium">Nothing has been shared with you yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="py-3 px-6">Name</th>
                                <th class="py-3 px-6">Type</th>
                                <th class="py-3 px-6">Shared By</th>
                                <th class="py-3 px-6">Permission</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($shares as $share)
                                @php
                                    $item = $share->shareable;
                                @endphp
                                @if($item)
                                <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                    <td class="py-4 px-6 font-semibold text-slate-800">
                                        <div class="flex items-center space-x-3">
                                            @if($share->shareable_type === 'App\Models\Folder')
                                                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                                </div>
                                                <a href="{{ route('drive.index', $item->id) }}" class="hover:text-indigo-600 transition-colors">
                                                    {{ $item->nama }}
                                                </a>
                                            @else
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <a href="{{ route('documents.show', $item->id) }}" class="hover:text-indigo-600 transition-colors">
                                                    {{ $item->nama }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                                        {{ class_basename($share->shareable_type) }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-600 font-medium">
                                        {{ $share->sharedBy?->nama ?? 'System' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide
                                            @if($share->permission === 'manager') bg-rose-50 text-rose-700
                                            @elseif($share->permission === 'editor') bg-indigo-50 text-indigo-700
                                            @else bg-slate-100 text-slate-700 @endif">
                                            {{ $share->permission }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            @if($share->shareable_type === 'App\Models\Document')
                                                <a href="{{ route('documents.preview', $item->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Preview">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('documents.download', $item->id) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Download">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
