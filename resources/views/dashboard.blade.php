<x-app-layout>
    <div class="space-y-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
                <p class="text-slate-500 text-sm mt-1">Overview of your documents, folders, and storage usage.</p>
            </div>
            <div>
                <a href="{{ route('drive.index') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/10 transition-all hover:bg-indigo-700 sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Go to My Drive
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 xl:grid-cols-3">
            <!-- Total Folders Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex items-center space-x-5">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Total Folders</span>
                    <span class="text-2xl font-bold text-slate-800 mt-1 block">{{ $totalFolders }}</span>
                </div>
            </div>

            <!-- Total Documents Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex items-center space-x-5">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Total Files</span>
                    <span class="text-2xl font-bold text-slate-800 mt-1 block">{{ $totalDocuments }}</span>
                </div>
            </div>

            <!-- Storage Used Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center space-x-5 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Storage Used</span>
                        <span class="text-2xl font-bold text-slate-800 mt-1 block">
                            {{ number_format($user->storage_used / 1048576, 2) }} MB
                        </span>
                    </div>
                </div>

                @php
                    $percentage = $user->storage_quota > 0 ? min(100, round(($user->storage_used / $user->storage_quota) * 100, 1)) : 0;
                @endphp
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mb-1">
                    <div class="bg-indigo-600 h-full rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] font-medium text-slate-500">
                    <span>{{ $percentage }}% used</span>
                    <span>Limit: {{ $user->storage_quota > 0 ? (number_format($user->storage_quota / 1073741824, 2) . ' GB') : 'Unlimited' }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Documents -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">Recent Documents</h2>
                <a href="{{ route('drive.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold transition-colors">View All</a>
            </div>

            @if($recentDocuments->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    </div>
                    <p class="text-slate-500 font-medium">No recent documents found.</p>
                    <p class="text-slate-400 text-sm mt-1">Upload a file in My Drive to get started.</p>
                </div>
            @else
                <div data-responsive-card-list class="divide-y divide-slate-100 lg:hidden">
                    @foreach($recentDocuments as $doc)
                        <article class="space-y-4 p-4 sm:p-5">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('documents.show', $doc->id) }}" class="block break-words text-sm font-semibold text-slate-800 transition-colors hover:text-indigo-600">{{ $doc->nama }}</a>
                                    <p class="break-all text-xs text-slate-400">{{ $doc->nama_file_asli }}</p>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Category</dt>
                                    <dd class="mt-1">
                                        @if($doc->documentCategory)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background-color: {{ $doc->documentCategory->warna }}20; color: {{ $doc->documentCategory->warna }}">{{ $doc->documentCategory->nama }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">Uncategorized</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Size</dt>
                                    <dd class="mt-1 font-medium text-slate-600">{{ $doc->formattedSize() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Uploaded At</dt>
                                    <dd class="mt-1 text-slate-500">{{ $doc->created_at->diffForHumans() }}</dd>
                                </div>
                            </dl>
                            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-3">
                                <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50" aria-label="Preview {{ $doc->nama }}">Preview</a>
                                <a href="{{ route('documents.download', $doc->id) }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-emerald-600 hover:bg-emerald-50" aria-label="Download {{ $doc->nama }}">Download</a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div data-responsive-desktop-table class="hidden lg:block">
                    <table class="w-full table-fixed text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="px-2 py-3 xl:px-6">Name</th>
                                <th class="px-2 py-3 xl:px-6">Category</th>
                                <th class="whitespace-nowrap px-2 py-3 xl:px-6">Size</th>
                                <th class="px-2 py-3 xl:px-6">Uploaded At</th>
                                <th class="px-2 py-3 text-right xl:px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentDocuments as $doc)
                                <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                    <td class="px-2 py-4 font-semibold text-slate-800 xl:px-6">
                                        <div class="flex items-center space-x-3">
                                            <!-- SVG Document Icon -->
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div class="min-w-0 break-words">
                                                <a href="{{ route('documents.show', $doc->id) }}" class="hover:text-indigo-600 transition-colors">
                                                    {{ $doc->nama }}
                                                </a>
                                                <span class="block break-all text-[10px] text-slate-400">{{ $doc->nama_file_asli }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-4 xl:px-6">
                                        @if($doc->documentCategory)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $doc->documentCategory->warna }}20; color: {{ $doc->documentCategory->warna }}">
                                                {{ $doc->documentCategory->nama }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-2 py-4 font-medium text-slate-500 xl:px-6">
                                        {{ $doc->formattedSize() }}
                                    </td>
                                    <td class="px-2 py-4 text-slate-400 xl:px-6">
                                        {{ $doc->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-2 py-4 text-right xl:px-6">
                                        <div class="flex flex-wrap items-center justify-end gap-1 xl:gap-2">
                                            <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Preview" aria-label="Preview {{ $doc->nama }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('documents.download', $doc->id) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Download" aria-label="Download {{ $doc->nama }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                            <a href="{{ route('documents.show', $doc->id) }}" class="rounded-lg p-1.5 text-slate-400 transition-all hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2" title="Details" aria-label="View details for {{ $doc->nama }}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
