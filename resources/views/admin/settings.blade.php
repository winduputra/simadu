<x-app-layout>
    <div class="space-y-8 max-w-3xl" x-data="{ 
        testing: false, 
        resultSuccess: null, 
        resultMessage: '',
        testPath() {
            const nasPath = document.getElementById('nas_path').value;
            if(!nasPath) {
                alert('Please enter a NAS path to test.');
                return;
            }
            this.testing = true;
            this.resultSuccess = null;
            this.resultMessage = '';
            
            fetch('{{ route('admin.settings.test-connection') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nas_path: nasPath })
            })
            .then(res => res.json())
            .then(data => {
                this.testing = false;
                this.resultSuccess = data.success;
                this.resultMessage = data.message;
            })
            .catch(err => {
                this.testing = false;
                this.resultSuccess = false;
                this.resultMessage = 'Network error: ' + err;
            });
        }
    }">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-800">System Settings</h1>
            <p class="text-slate-500 text-sm mt-1">Configure global infrastructure path for storage and default account quota.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-6">
                    <!-- NAS Path setting -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Synology NAS Local Path / Storage Path</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" name="nas_path" id="nas_path" required placeholder="e.g. C:\laragon\www\simadu\storage\app\documents" value="{{ old('nas_path', $nasPath) }}" class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="button" @click="testPath()" class="flex w-full shrink-0 items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-slate-800 sm:w-auto sm:min-w-[140px]" :disabled="testing">
                                <span x-show="!testing">Test Connection</span>
                                <span x-show="testing" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Testing...
                                </span>
                            </button>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Specify the local path mounted to the Synology NAS directory via LAN. This folder must exist and be writable by Laravel.</span>
                        @error('nas_path') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Connection test result panel -->
                    <div x-show="resultSuccess !== null" class="p-4 border rounded-xl flex items-start space-x-3 transition-all" :class="resultSuccess ? 'bg-emerald-50 border-emerald-250 text-emerald-800' : 'bg-rose-50 border-rose-250 text-rose-800'" x-cloak>
                        <div class="shrink-0 mt-0.5">
                            <!-- Success SVG -->
                            <svg x-show="resultSuccess" class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <!-- Error SVG -->
                            <svg x-show="!resultSuccess" class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-sm font-semibold flex-1">
                            <span x-text="resultMessage"></span>
                        </div>
                    </div>

                    <!-- Default Storage Quota setting -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Default Storage Quota for New Users (in Bytes)</label>
                        <input type="number" name="default_storage_quota" required min="0" placeholder="e.g. 1073741824. 0 for Unlimited" value="{{ old('default_storage_quota', $defaultQuota) }}" class="w-full max-w-xs px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">Specify in bytes. 1 GB = 1,073,741,824 bytes. Set to 0 for unlimited.</span>
                        @error('default_storage_quota') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-100">
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/10 transition-all hover:bg-indigo-700 sm:w-auto">
                        Save System Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
