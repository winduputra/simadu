<x-app-layout>
    <div class="space-y-8 max-w-2xl">
        <!-- Header -->
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-500 font-medium">
                <a href="{{ route('admin.categories.index') }}" class="hover:text-indigo-600 transition-colors">Categories</a>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-400">Add Category</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800 mt-2">Add New Category</h1>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Category Name</label>
                        <input type="text" name="nama" required placeholder="e.g. Surat Keputusan" value="{{ old('nama') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('nama') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tag Accent Color</label>
                            <input type="color" name="warna" value="{{ old('warna', '#3B82F6') }}" class="w-full h-11 px-1 py-1 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                            @error('warna') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Lucide Icon String</label>
                            <input type="text" name="ikon" placeholder="e.g. file-text" value="{{ old('ikon', 'file') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('ikon') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                        <textarea name="deskripsi" rows="4" placeholder="Brief description of the category..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
