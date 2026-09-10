<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Categories Management</h1>
                <p class="text-slate-500 text-sm mt-1">Manage tags/categories (e.g. MoU, Laporan, Surat Keputusan) assigned to documents.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-700 sm:w-auto">
                Add Category
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div data-responsive-card-list class="divide-y divide-slate-100 lg:hidden">
                @foreach($categories as $cat)
                    <article class="space-y-4 p-4 sm:p-5">
                        <div class="min-w-0">
                            <h2 class="break-words text-sm font-semibold text-slate-800">{{ $cat->nama }}</h2>
                            <p class="mt-1 break-all text-xs text-slate-400">{{ $cat->slug }}</p>
                        </div>
                        <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tag Color</dt>
                                <dd class="mt-1 flex items-center gap-2 text-slate-600">
                                    <span class="h-4 w-4 rounded-full border border-slate-200" style="background-color: {{ $cat->warna }}"></span>
                                    <span class="text-xs font-medium">{{ $cat->warna }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Icon</dt>
                                <dd class="mt-1 text-slate-600">{{ $cat->ikon }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Description</dt>
                                <dd class="mt-1 break-words text-slate-600">{{ $cat->deskripsi ?? '-' }}</dd>
                            </div>
                        </dl>
                        <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                            <a href="{{ route('admin.categories.edit', $cat->id) }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50" aria-label="Edit {{ $cat->nama }}">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" x-data @submit.prevent="if(confirm('Yakin ingin menghapus data kategori ini?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="min-h-10 rounded-lg px-3 text-sm font-semibold text-rose-600 hover:bg-rose-50" aria-label="Delete {{ $cat->nama }}">Delete</button>
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
                            <th class="px-2 py-3 xl:px-6">Slug</th>
                            <th class="px-2 py-3 xl:px-6">Tag Color</th>
                            <th class="px-2 py-3 xl:px-6">Icon</th>
                            <th class="px-2 py-3 xl:px-6">Description</th>
                            <th class="px-2 py-3 text-right xl:px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($categories as $cat)
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                <td class="break-words px-2 py-4 font-semibold text-slate-800 xl:px-6">
                                    {{ $cat->nama }}
                                </td>
                                <td class="break-all px-2 py-4 text-xs text-slate-500 xl:px-6">
                                    {{ $cat->slug }}
                                </td>
                                <td class="px-2 py-4 xl:px-6">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-4 h-4 rounded-full border border-slate-200" style="background-color: {{ $cat->warna }}"></span>
                                        <span class="text-xs font-medium text-slate-600">{{ $cat->warna }}</span>
                                    </div>
                                </td>
                                <td class="break-words px-2 py-4 text-slate-500 xl:px-6">
                                    {{ $cat->ikon }}
                                </td>
                                <td class="break-words px-2 py-4 text-slate-400 xl:px-6">
                                    {{ $cat->deskripsi ?? '-' }}
                                </td>
                                <td class="px-2 py-4 text-right xl:px-6">
                                    <div class="flex flex-wrap items-center justify-end gap-1 xl:gap-2">
                                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Edit" aria-label="Edit {{ $cat->nama }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline-block" x-data @submit.prevent="if(confirm('Yakin ingin menghapus data kategori ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-all" title="Hapus" aria-label="Delete {{ $cat->nama }}">
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
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
