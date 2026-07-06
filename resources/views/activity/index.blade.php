<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Activity Log</h1>
                <p class="text-slate-500 text-sm mt-1">Audit log of actions performed on folders, documents, and shares.</p>
            </div>

            <!-- Action Filter -->
            <form action="{{ route('activity.index') }}" method="GET" class="flex items-center space-x-3">
                <select name="aksi" class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                    <option value="">All Actions</option>
                    <option value="upload" {{ request('aksi') === 'upload' ? 'selected' : '' }}>Upload</option>
                    <option value="delete" {{ request('aksi') === 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="restore" {{ request('aksi') === 'restore' ? 'selected' : '' }}>Restore</option>
                    <option value="rename" {{ request('aksi') === 'rename' ? 'selected' : '' }}>Rename</option>
                    <option value="move" {{ request('aksi') === 'move' ? 'selected' : '' }}>Move</option>
                    <option value="share" {{ request('aksi') === 'share' ? 'selected' : '' }}>Share</option>
                    <option value="unshare" {{ request('aksi') === 'unshare' ? 'selected' : '' }}>Unshare</option>
                </select>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-slate-500 font-medium">No activity logs found.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="py-3 px-6">User</th>
                                <th class="py-3 px-6">Action</th>
                                <th class="py-3 px-6">Target Item</th>
                                <th class="py-3 px-6">Details</th>
                                <th class="py-3 px-6">IP / Agent</th>
                                <th class="py-3 px-6">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($logs as $log)
                                <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                    <td class="py-4 px-6 font-semibold text-slate-800">
                                        {{ $log->user?->nama ?? 'Unknown' }}
                                        <span class="text-[10px] text-slate-400 block">{{ $log->user?->nip }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide
                                            @if(in_array($log->aksi, ['upload', 'restore', 'folder_create'])) bg-emerald-50 text-emerald-700
                                            @elseif(in_array($log->aksi, ['delete', 'force_delete', 'unshare'])) bg-rose-50 text-rose-700
                                            @else bg-amber-50 text-amber-700 @endif">
                                            {{ $log->aksi }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-medium text-slate-700">
                                        @if($log->loggable)
                                            <span class="text-slate-900">{{ $log->loggable->nama ?? 'Item' }}</span>
                                            <span class="text-[10px] text-slate-400 block uppercase tracking-wider">{{ class_basename($log->loggable_type) }}</span>
                                        @else
                                            <span class="text-slate-400 italic">No Target / Deleted</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 max-w-xs truncate">
                                        @if($log->detail)
                                            <code class="text-xs bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded text-slate-600 block truncate">
                                                {{ json_encode($log->detail) }}
                                            </code>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-400">
                                        <span class="font-medium text-slate-500">{{ $log->ip_address }}</span>
                                        <span class="block truncate max-w-[150px]" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-400 text-xs">
                                        {{ $log->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
