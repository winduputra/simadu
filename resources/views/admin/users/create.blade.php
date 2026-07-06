<x-app-layout>
    <div class="space-y-8 max-w-2xl">
        <!-- Header -->
        <div>
            <nav class="flex items-center space-x-2 text-sm text-slate-500 font-medium">
                <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 transition-colors">Users</a>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-400">Add User</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800 mt-2">Add New User</h1>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">NIP (Government ID)</label>
                        <input type="text" name="nip" required placeholder="Enter NIP..." value="{{ old('nip') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('nip') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" name="nama" required placeholder="Enter full name..." value="{{ old('nama') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('nama') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="Enter email..." value="{{ old('email') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Role</label>
                        <select name="role_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Unit Kerja</label>
                        <select name="unit_kerja_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">[None]</option>
                            @foreach($unitKerjas as $uk)
                                <option value="{{ $uk->id }}" {{ old('unit_kerja_id') == $uk->id ? 'selected' : '' }}>{{ $uk->nama }} ({{ $uk->kode }})</option>
                            @endforeach
                        </select>
                        @error('unit_kerja_id') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Storage Quota (in Bytes)</label>
                        <input type="number" name="storage_quota" required min="0" placeholder="e.g. 1073741824 for 1GB. 0 for Unlimited" value="{{ old('storage_quota', 0) }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">1 GB = 1,073,741,824 bytes. Set to 0 for unlimited quota.</span>
                        @error('storage_quota') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" required placeholder="Enter password..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Re-enter password..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
