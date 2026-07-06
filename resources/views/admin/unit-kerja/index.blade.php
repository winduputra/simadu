<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Unit Kerja Management</h1>
                <p class="text-slate-500 text-sm mt-1">Manage corporate divisions/units for file sharing purposes.</p>
            </div>
            <a href="{{ route('admin.unit-kerja.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                Add Unit
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-6">Code</th>
                            <th class="py-3 px-6">Name</th>
                            <th class="py-3 px-6">Description</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($unitKerjas as $uk)
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700">
                                <td class="py-4 px-6 font-bold text-indigo-600">
                                    {{ $uk->kode }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-800">
                                    {{ $uk->nama }}
                                </td>
                                <td class="py-4 px-6 text-slate-400">
                                    {{ $uk->deskripsi ?? '-' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.unit-kerja.edit', $uk->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.unit-kerja.destroy', $uk->id) }}" method="POST" class="inline-block" x-data @submit.prevent="if(confirm('Yakin ingin menghapus data ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-all" title="Hapus">
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
                {{ $unitKerjas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
