<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
                <p class="text-slate-500 text-sm mt-1">Manage employee accounts, roles, division assignments, and storage quotas.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-700 sm:w-auto">
                Add User
            </a>
        </div>

        <!-- Users Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div data-responsive-card-list class="divide-y divide-slate-100 lg:hidden">
                @foreach($users as $user)
                    <article class="space-y-4 p-4 sm:p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-semibold text-slate-800">{{ $user->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $user->nip }}</p>
                            </div>
                            @if($user->is_active)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700">Inactive</span>
                            @endif
                        </div>
                        <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                            <div class="min-w-0">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</dt>
                                <dd class="mt-1 break-all text-slate-600">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Role</dt>
                                <dd class="mt-1 capitalize font-medium text-slate-700">{{ $user->role?->nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Unit Kerja</dt>
                                <dd class="mt-1 text-slate-600">
                                    @if($user->unitKerja)
                                        {{ $user->unitKerja->nama }} ({{ $user->unitKerja->kode }})
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Quota</dt>
                                <dd class="mt-1 font-medium text-slate-700">{{ number_format($user->storage_used / 1048576, 1) }} MB used</dd>
                                <dd class="text-xs text-slate-400">Limit: {{ $user->storage_quota > 0 ? (number_format($user->storage_quota / 1073741824, 1) . ' GB') : 'Unlimited' }}</dd>
                            </div>
                        </dl>
                        <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50" aria-label="Edit {{ $user->nama }}">Edit</a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" x-data @submit.prevent="if(confirm('Yakin ingin menghapus pengguna ini?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="min-h-10 rounded-lg px-3 text-sm font-semibold text-rose-600 hover:bg-rose-50" aria-label="Delete {{ $user->nama }}">Delete</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div data-responsive-desktop-table class="hidden lg:block">
                <table class="w-full table-fixed text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="px-2 py-3 xl:px-6">Name / NIP</th>
                            <th class="px-2 py-3 xl:px-6">Email</th>
                            <th class="px-2 py-3 xl:px-6">Role</th>
                            <th class="px-2 py-3 xl:px-6">Unit Kerja</th>
                            <th class="px-2 py-3 xl:px-6">Quota</th>
                            <th class="px-2 py-3 xl:px-6">Status</th>
                            <th class="px-2 py-3 text-right xl:px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                <td class="break-words px-2 py-4 font-semibold text-slate-800 xl:px-6">
                                    {{ $user->nama }}
                                    <span class="text-[10px] text-slate-400 block font-normal">{{ $user->nip }}</span>
                                </td>
                                <td class="break-all px-2 py-4 text-slate-500 xl:px-6">
                                    {{ $user->email }}
                                </td>
                                <td class="break-words px-2 py-4 font-medium capitalize text-slate-700 xl:px-6">
                                    {{ $user->role?->nama }}
                                </td>
                                <td class="break-words px-2 py-4 xl:px-6">
                                    @if($user->unitKerja)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $user->unitKerja->nama }} ({{ $user->unitKerja->kode }})
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="break-words px-2 py-4 xl:px-6">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-800">{{ number_format($user->storage_used / 1048576, 1) }} MB used</span>
                                        <span class="text-[10px] text-slate-400">Limit: {{ $user->storage_quota > 0 ? (number_format($user->storage_quota / 1073741824, 1) . ' GB') : 'Unlimited' }}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-4 xl:px-6">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-2 py-4 text-right xl:px-6">
                                    <div class="flex flex-wrap items-center justify-end gap-1 xl:gap-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Edit" aria-label="Edit {{ $user->nama }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" x-data @submit.prevent="if(confirm('Yakin ingin menghapus pengguna ini?')) $el.submit()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-all" title="Hapus" aria-label="Delete {{ $user->nama }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
