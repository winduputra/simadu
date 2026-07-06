<x-app-layout>
    <div class="space-y-8">
        <!-- Dashboard Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
                <p class="text-slate-500 text-sm mt-1">Overview of your documents, folders, and storage usage.</p>
            </div>
            <div>
                <a href="{{ route('drive.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-indigo-600/10">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Go to My Drive
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Folders Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex items-center space-x-5 hover:border-slate-300 transition-colors">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Total Folders</span>
                    <span class="text-2xl font-bold text-slate-800 mt-1 block">{{ $totalFolders }}</span>
                </div>
            </div>

            <!-- Total Documents Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex items-center space-x-5 hover:border-slate-300 transition-colors">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Total Files</span>
                    <span class="text-2xl font-bold text-slate-800 mt-1 block">{{ $totalDocuments }}</span>
                </div>
            </div>

            <!-- Storage Used Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:border-slate-300 transition-colors">
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
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="py-3 px-6">Name</th>
                                <th class="py-3 px-6">Category</th>
                                <th class="py-3 px-6">Size</th>
                                <th class="py-3 px-6">Uploaded At</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentDocuments as $doc)
                                <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                    <td class="py-4 px-6 font-semibold text-slate-800">
                                        <div class="flex items-center space-x-3">
                                            <!-- SVG Document Icon -->
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div class="truncate max-w-xs">
                                                <a href="{{ route('documents.show', $doc->id) }}" class="hover:text-indigo-600 transition-colors">
                                                    {{ $doc->nama }}
                                                </a>
                                                <span class="text-[10px] text-slate-400 block truncate">{{ $doc->nama_file_asli }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($doc->documentCategory)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $doc->documentCategory->warna }}20; color: {{ $doc->documentCategory->warna }}">
                                                {{ $doc->documentCategory->nama }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 font-medium">
                                        {{ $doc->formattedSize() }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-400">
                                        {{ $doc->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Preview">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('documents.download', $doc->id) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Download">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
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
