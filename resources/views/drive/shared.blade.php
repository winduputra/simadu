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
                <div data-responsive-card-list class="divide-y divide-slate-100 lg:hidden">
                    @foreach($shares as $share)
                        @php
                            $item = $share->shareable;
                        @endphp
                        @if($item)
                            <article class="space-y-4 p-4 sm:p-5">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $share->shareable_type === 'App\Models\Folder' ? 'bg-amber-50 text-amber-500' : 'bg-blue-50 text-blue-500' }}">
                                        @if($share->shareable_type === 'App\Models\Folder')
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                        @else
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $share->shareable_type === 'App\Models\Folder' ? route('drive.index', $item->id) : route('documents.show', $item->id) }}" class="block break-words text-sm font-semibold text-slate-800 transition-colors hover:text-indigo-600">
                                            {{ $item->nama }}
                                        </a>
                                        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ class_basename($share->shareable_type) }}</p>
                                    </div>
                                </div>
                                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Shared By</dt>
                                        <dd class="mt-1 font-medium text-slate-600">{{ $share->sharedBy?->nama ?? 'System' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Permission</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide
                                                @if($share->permission === 'manager') bg-rose-50 text-rose-700
                                                @elseif($share->permission === 'editor') bg-indigo-50 text-indigo-700
                                                @else bg-slate-100 text-slate-700 @endif">
                                                {{ $share->permission }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                                @if($share->shareable_type === 'App\Models\Document')
                                    <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-3">
                                        <a href="{{ route('documents.preview', $item->id) }}" target="_blank" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50" aria-label="Preview {{ $item->nama }}">Preview</a>
                                        <a href="{{ route('documents.download', $item->id) }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-emerald-600 hover:bg-emerald-50" aria-label="Download {{ $item->nama }}">Download</a>
                                    </div>
                                @endif
                            </article>
                        @endif
                    @endforeach
                </div>
                <div data-responsive-desktop-table class="hidden lg:block">
                    <table class="w-full table-fixed text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="px-2 py-3 xl:px-6">Name</th>
                                <th class="px-2 py-3 xl:px-6">Type</th>
                                <th class="px-2 py-3 xl:px-6">Shared By</th>
                                <th class="px-2 py-3 xl:px-6">Permission</th>
                                <th class="px-2 py-3 text-right xl:px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($shares as $share)
                                @php
                                    $item = $share->shareable;
                                @endphp
                                @if($item)
                                <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                    <td class="px-2 py-4 font-semibold text-slate-800 xl:px-6">
                                        <div class="flex min-w-0 items-center space-x-3">
                                            @if($share->shareable_type === 'App\Models\Folder')
                                                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                                </div>
                                                <a href="{{ route('drive.index', $item->id) }}" class="min-w-0 break-words transition-colors hover:text-indigo-600" title="{{ $item->nama }}">
                                                    {{ $item->nama }}
                                                </a>
                                            @else
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <a href="{{ route('documents.show', $item->id) }}" class="min-w-0 break-words transition-colors hover:text-indigo-600" title="{{ $item->nama }}">
                                                    {{ $item->nama }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="break-words px-2 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 xl:px-6">
                                        {{ class_basename($share->shareable_type) }}
                                    </td>
                                    <td class="break-words px-2 py-4 font-medium text-slate-600 xl:px-6">
                                        {{ $share->sharedBy?->nama ?? 'System' }}
                                    </td>
                                    <td class="px-2 py-4 xl:px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide
                                            @if($share->permission === 'manager') bg-rose-50 text-rose-700
                                            @elseif($share->permission === 'editor') bg-indigo-50 text-indigo-700
                                            @else bg-slate-100 text-slate-700 @endif">
                                            {{ $share->permission }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 text-right xl:px-6">
                                        <div class="flex flex-wrap items-center justify-end gap-1 xl:gap-2">
                                            @if($share->shareable_type === 'App\Models\Document')
                                                <a href="{{ route('documents.preview', $item->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Preview" aria-label="Preview {{ $item->nama }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('documents.download', $item->id) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Download" aria-label="Download {{ $item->nama }}">
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
