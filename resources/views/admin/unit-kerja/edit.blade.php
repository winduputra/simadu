<x-app-layout>
    <div class="space-y-8 max-w-2xl">
        <!-- Header -->
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-500 font-medium">
                <a href="{{ route('admin.unit-kerja.index') }}" class="hover:text-indigo-600 transition-colors">Unit Kerja</a>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-400">Edit Unit</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800 mt-2">Edit Unit Kerja: {{ $unitKerja->nama }}</h1>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <form action="{{ route('admin.unit-kerja.update', $unitKerja->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Unit Code</label>
                        <input type="text" name="kode" required placeholder="e.g. BU" value="{{ old('kode', $unitKerja->kode) }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('kode') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Unit Name</label>
                        <input type="text" name="nama" required placeholder="e.g. Bagian Umum" value="{{ old('nama', $unitKerja->nama) }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('nama') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                        <textarea name="deskripsi" rows="4" placeholder="Brief description..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('deskripsi', $unitKerja->deskripsi) }}</textarea>
                        @error('deskripsi') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.unit-kerja.index') }}" class="w-full rounded-xl bg-slate-100 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition-all hover:bg-slate-250 sm:w-auto">Cancel</a>
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-700 sm:w-auto">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
