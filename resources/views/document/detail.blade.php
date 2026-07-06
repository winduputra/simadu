<x-app-layout>
    <div class="space-y-8" x-data="{ 
        showShare: false, 
        showPublicLink: false, 
        showNewVersion: false 
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <a href="{{ route('drive.index') }}" class="hover:text-indigo-600 transition-colors">My Drive</a>
                    @if($document->folder)
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <a href="{{ route('drive.index', $document->folder->id) }}" class="hover:text-indigo-600 transition-colors">{{ $document->folder->nama }}</a>
                    @endif
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-slate-400">{{ $document->nama }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-800 mt-2">
                    {{ $document->nama }}
                </h1>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('documents.preview', $document->id) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl transition-all shadow-sm">
                    Preview
                </a>
                <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-indigo-600/10">
                    Download
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Columns: Info & Versioning -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Document Info Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 mb-6">Document Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Original Filename</span>
                            <span class="text-slate-700 font-medium mt-1 block truncate" title="{{ $document->nama_file_asli }}">{{ $document->nama_file_asli }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Owner / Creator</span>
                            <span class="text-slate-700 font-medium mt-1 block">{{ $document->user?->nama }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Mime Type</span>
                            <span class="text-slate-700 font-medium mt-1 block">{{ $document->mime_type }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Size</span>
                            <span class="text-slate-700 font-medium mt-1 block">{{ $document->formattedSize() }}</span>
                        </div>
                    </div>

                    <!-- Category Assignment -->
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <form action="{{ route('documents.category.update', $document->id) }}" method="POST" class="max-w-xs">
                            @csrf
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                            <div class="flex space-x-2">
                                <select name="document_category_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">No Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $document->document_category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Version History -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-slate-800">Version History</h2>
                        <button @click="showNewVersion = true" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl hover:bg-indigo-100 transition-colors">
                            Upload New Version
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach($document->versions as $ver)
                            <div class="flex items-start justify-between p-4 border border-slate-100 rounded-xl hover:bg-slate-50 transition-colors">
                                <div class="space-y-1.5 flex-1 min-w-0 mr-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-extrabold rounded-md">V{{ $ver->versi }}</span>
                                        <span class="text-slate-800 font-semibold text-sm truncate">{{ $ver->nama_file }}</span>
                                    </div>
                                    @if($ver->catatan)
                                        <p class="text-xs text-slate-500 italic bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $ver->catatan }}</p>
                                    @endif
                                    <div class="text-[11px] text-slate-400">
                                        Uploaded by <span class="font-medium text-slate-500">{{ $ver->user?->nama }}</span> on {{ $ver->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 shrink-0">
                                    <a href="{{ route('versions.download', [$document->id, $ver->versi]) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Download this version">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @if($ver->versi !== $document->current_version)
                                        <form action="{{ route('versions.rollback', [$document->id, $ver->versi]) }}" method="POST" onsubmit="return confirm('Rollback document to version {{ $ver->versi }}?');">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-amber-600 rounded-lg hover:bg-slate-100 transition-all" title="Rollback to this version">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Sharing & Public Links -->
            <div class="space-y-8">
                <!-- Sharing Settings -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">Sharing</h2>

                    <!-- Shared List -->
                    <div class="space-y-3 mb-6">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Shared with</div>
                        @if($document->shares->isEmpty())
                            <p class="text-xs text-slate-400 italic">Not shared with anyone yet.</p>
                        @else
                            @foreach($document->shares as $share)
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl text-xs">
                                    <div class="min-w-0 flex-1 mr-2">
                                        <span class="font-bold text-slate-700 block truncate">
                                            @if($share->shared_to_type === 'App\Models\User')
                                                {{ $share->sharedTo?->nama }} (User)
                                            @else
                                                {{ $share->sharedTo?->nama }} (Unit Kerja)
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-slate-400 capitalize block">{{ $share->permission }}</span>
                                    </div>
                                    <form action="{{ route('shares.destroy', $share->id) }}" method="POST" onsubmit="return confirm('Remove access for this recipient?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Share Form Button -->
                    <button @click="showShare = true" class="w-full inline-flex items-center justify-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        Add Recipients
                    </button>
                </div>

                <!-- Public Links settings -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">Public Links</h2>

                    <!-- Active Public Link URL if stored in session -->
                    @if (session('public_link_url'))
                        <div class="mb-4 p-3 bg-indigo-50 border border-indigo-100 rounded-xl">
                            <span class="text-xs font-semibold text-indigo-800 block mb-1">Generated Link</span>
                            <div class="flex items-center space-x-2">
                                <input type="text" readonly value="{{ session('public_link_url') }}" class="w-full text-xs px-2 py-1 border border-slate-200 rounded bg-white text-slate-600 select-all">
                            </div>
                        </div>
                    @endif

                    @php
                        $publicLinks = \App\Models\PublicLink::where('linkable_type', 'App\Models\Document')
                            ->where('linkable_id', $document->id)
                            ->where('is_active', true)
                            ->get();
                    @endphp

                    <div class="space-y-3 mb-6">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Links</div>
                        @if($publicLinks->isEmpty())
                            <p class="text-xs text-slate-400 italic">No active public links.</p>
                        @else
                            @foreach($publicLinks as $pl)
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl space-y-1.5 relative">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 capitalize">{{ $pl->permission }} Link</span>
                                        <form action="{{ route('public-links.revoke', $pl->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded">
                                                Revoke
                                            </button>
                                        </form>
                                    </div>
                                    <input type="text" readonly value="{{ route('public.access', $pl->token) }}" class="w-full text-[10px] px-2 py-1 border border-slate-200 rounded bg-white text-slate-500 select-all">
                                    <div class="text-[9px] text-slate-400">
                                        Access Count: {{ $pl->access_count }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Generate link button -->
                    <button @click="showPublicLink = true" class="w-full inline-flex items-center justify-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        Create Public Link
                    </button>
                </div>
            </div>
        </div>

        <!-- Modals -->

        <!-- Add Recipients Share Modal -->
        <div x-show="showShare" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="showShare = false">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Share Document</h3>
                <form action="{{ route('shares.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shareable_type" value="document">
                    <input type="hidden" name="shareable_id" value="{{ $document->id }}">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Share To</label>
                            <select name="shared_to_type" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" x-model="recipientType">
                                <option value="user">User / Employee</option>
                                <option value="unit">Unit Kerja / Division</option>
                            </select>
                        </div>

                        <!-- User Recipient -->
                        <div x-show="recipientType === 'user'" x-init="recipientType = 'user'">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Employee</label>
                            <select name="shared_to_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" x-bind:disabled="recipientType !== 'user'">
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->nip }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit Recipient -->
                        <div x-show="recipientType === 'unit'">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Unit Kerja</label>
                            <select name="shared_to_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" x-bind:disabled="recipientType !== 'unit'">
                                @foreach($unitKerjas as $uk)
                                    <option value="{{ $uk->id }}">{{ $uk->nama }} ({{ $uk->kode }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Permission Tier</label>
                            <select name="permission" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="viewer">Viewer (Read Only)</option>
                                <option value="editor">Editor (Can Upload & Edit)</option>
                                <option value="manager">Manager (Full Access & Delete)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showShare = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Share</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Public Link Modal -->
        <div x-show="showPublicLink" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="showPublicLink = false">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Create Public Link</h3>
                <form action="{{ route('public-links.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="linkable_type" value="document">
                    <input type="hidden" name="linkable_id" value="{{ $document->id }}">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Access Level</label>
                            <select name="permission" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="viewer">Viewer (Can View/Stream only)</option>
                                <option value="editor">Editor (Can View and Download)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password (Optional)</label>
                            <input type="password" name="password" placeholder="Protect with password..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Expiration Date (Optional)</label>
                            <input type="datetime-local" name="expires_at" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Max Access Count (Optional)</label>
                            <input type="number" name="max_access_count" min="1" placeholder="Unlimited" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showPublicLink = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Generate Link</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload New Version Modal -->
        <div x-show="showNewVersion" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="showNewVersion = false">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Upload New Version</h3>
                <form action="{{ route('versions.store', $document->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Select File</label>
                            <input type="file" name="file" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Version Notes</label>
                            <textarea name="catatan" rows="3" placeholder="What changed in this version?..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showNewVersion = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
