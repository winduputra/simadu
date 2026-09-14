<!-- Bulk Share Modal -->
<div x-show="showBulkShareModal" x-init="$watch('showBulkShareModal', value => value ? activateDialog($el) : restoreDialogFocus($nextTick))" tabindex="-1" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm sm:items-center" x-cloak role="dialog" aria-modal="true" aria-labelledby="bulk-share-title">
    <div class="my-4 max-h-[calc(100vh-2rem)] w-full max-w-lg overflow-y-auto rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl sm:p-6" @click.away="showBulkShareModal = false" x-data="{ targetType: 'user' }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 id="bulk-share-title" class="text-lg font-bold text-slate-800">Share selected items</h3>
                <p class="mt-1 text-sm text-slate-500"><span x-text="selectedItems.length"></span> item will receive the same internal access.</p>
            </div>
            <button type="button" @click="showBulkShareModal = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" aria-label="Close bulk share dialog">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('shares.bulk-store') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <template x-for="id in selectedIds('folder')" :key="`share-folder-${id}`">
                <input type="hidden" name="folder_ids[]" :value="id">
            </template>
            <template x-for="id in selectedIds('document')" :key="`share-document-${id}`">
                <input type="hidden" name="document_ids[]" :value="id">
            </template>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label for="bulk-shared-to-type" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Share Target</label>
                    <select id="bulk-shared-to-type" name="shared_to_type" x-model="targetType" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="user">Specific User</option>
                        <option value="unit">Unit Kerja</option>
                    </select>
                </div>
                <div>
                    <label for="bulk-permission" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Permission</label>
                    <select id="bulk-permission" name="permission" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="viewer">Viewer (Read Only)</option>
                        <option value="editor">Editor (Upload/Edit)</option>
                        <option value="manager">Manager (Full Access)</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="bulk-user-recipient" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Recipient</label>
                    <select id="bulk-user-recipient" x-show="targetType === 'user'" :disabled="targetType !== 'user'" name="shared_to_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    <select x-show="targetType === 'unit'" :disabled="targetType !== 'unit'" name="shared_to_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($unitKerjas as $uk)
                            <option value="{{ $uk->id }}">{{ $uk->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" @click="showBulkShareModal = false" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Share items</button>
            </div>
        </form>
    </div>
</div>
