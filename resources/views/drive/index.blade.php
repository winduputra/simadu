<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <div class="space-y-8 relative min-h-[500px]" x-data="{ 
        showCreateFolder: false, 
        showUpload: false, 
        showRenameFolder: false, 
        showRenameFile: false,
        showMove: false,
        activeFolderId: null,
        activeFolderName: '',
        activeFileId: null,
        activeFileName: '',
        activeType: '',
        allFolders: @js($folders),
        isDragging: false,
        uploads: [],
        contextMenu: { show: false, x: 0, y: 0, type: '', id: null, name: '' },
        openContextMenu(e, type, id, name) {
            this.contextMenu.show = true;
            this.contextMenu.x = e.clientX;
            this.contextMenu.y = e.clientY;
            this.contextMenu.type = type;
            this.contextMenu.id = id;
            this.contextMenu.name = name;
        },
        handleDrop(e) {
            this.isDragging = false;
            let files = e.dataTransfer.files;
            if (!files.length) return;
            this.processFiles(files);
        },
        handleFileInput(e) {
            this.processFiles(e.target.files);
            this.showUpload = false;
        },
        processFiles(files) {
            let folderId = {{ $folder ? $folder->id : 'null' }};
            Array.from(files).forEach(file => {
                let uploadId = Date.now() + Math.random().toString(36).substr(2, 9);
                let controller = new AbortController();
                let upload = { id: uploadId, name: file.name, progress: 0, status: 'uploading', error: null, controller: controller };
                this.uploads.push(upload);
                
                let formData = new FormData();
                formData.append('files[]', file);
                if(folderId) formData.append('folder_id', folderId);
                
                axios.post('{{ route('documents.upload') }}', formData, {
                    signal: controller.signal,
                    headers: { 'Content-Type': 'multipart/form-data', 'Accept': 'application/json' },
                    onUploadProgress: (progressEvent) => {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        let target = this.uploads.find(u => u.id === uploadId);
                        if(target) target.progress = percentCompleted;
                    }
                }).then(res => {
                    let target = this.uploads.find(u => u.id === uploadId);
                    if(target) { target.status = 'completed'; target.progress = 100; }
                    // Reload only when all active uploads are done
                    if (!this.uploads.some(u => u.status === 'uploading')) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                }).catch(err => {
                    if (axios.isCancel(err)) {
                        return; // do nothing, already handled
                    }
                    let target = this.uploads.find(u => u.id === uploadId);
                    if(target) { target.status = 'error'; target.error = err.response?.data?.message || err.message; }
                    // Also check reload here in case other files finished but this one errored
                    if (!this.uploads.some(u => u.status === 'uploading')) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                });
            });
        },
        cancelUpload(uploadId) {
            let target = this.uploads.find(u => u.id === uploadId);
            if (target) {
                if (target.controller) {
                    target.controller.abort();
                }
                this.uploads = this.uploads.filter(u => u.id !== uploadId);
            }
        },
        clearUploads() {
            if (this.uploads.some(u => u.status === 'uploading')) {
                if (!confirm('Batalkan semua unggahan yang sedang berjalan?')) {
                    return;
                }
            }
            this.uploads.forEach(u => {
                if (u.status === 'uploading' && u.controller) {
                    u.controller.abort();
                }
            });
            this.uploads = [];
        }
    }" 
    @click="contextMenu.show = false" 
    @contextmenu.prevent="contextMenu.show = false"
    @dragover.prevent="isDragging = true" 
    @dragleave.prevent="isDragging = false" 
    @drop.prevent="handleDrop($event)">
        
        <!-- Drag and Drop Visual Overlay -->
        <div x-show="isDragging" x-transition class="absolute inset-0 z-50 bg-indigo-50/90 border-4 border-dashed border-indigo-500 rounded-2xl flex items-center justify-center pointer-events-none">
            <div class="text-center text-indigo-600">
                <svg class="w-20 h-20 mx-auto mb-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <h2 class="text-2xl font-bold">Lepaskan file di sini</h2>
                <p class="font-medium mt-1">File akan diunggah otomatis ke folder ini</p>
            </div>
        </div>
        <!-- Drive Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Breadcrumbs -->
                <nav class="flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <a href="{{ route('drive.index') }}" class="hover:text-indigo-600 transition-colors">My Drive</a>
                    @foreach($breadcrumbs as $bc)
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <a href="{{ route('drive.index', $bc->id) }}" class="hover:text-indigo-600 transition-colors">{{ $bc->nama }}</a>
                    @endforeach
                </nav>
                <h1 class="text-2xl font-bold text-slate-800 mt-2">
                    {{ $folder ? $folder->nama : 'My Drive' }}
                </h1>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center space-x-3">
                <button @click="showCreateFolder = true" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    New Folder
                </button>
                <button @click="showUpload = true" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-indigo-600/10">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Upload Files
                </button>
            </div>
        </div>

        <!-- Folders Section -->
        @if(!$folders->isEmpty())
        <div>
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Folders</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($folders as $f)
                    <div @contextmenu.stop.prevent="openContextMenu($event, 'folder', {{ $f->id }}, '{{ addslashes($f->nama) }}')" class="group bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex items-center justify-between relative cursor-context-menu">
                        <a href="{{ route('drive.index', $f->id) }}" class="flex items-center space-x-3 truncate flex-1 mr-2">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <span class="font-semibold text-slate-700 text-sm truncate group-hover:text-indigo-600 transition-colors">
                                {{ $f->nama }}
                            </span>
                        </a>

                        <!-- Dropdown Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-xl py-1.5 z-40">
                                <button @click="open = false; activeFolderId = {{ $f->id }}; activeFolderName = '{{ $f->nama }}'; showRenameFolder = true" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left">
                                    Rename
                                </button>
                                <button @click="open = false; activeFolderId = {{ $f->id }}; activeFolderName = '{{ $f->nama }}'; activeType = 'folder'; showMove = true" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left">
                                    Move to...
                                </button>
                                <hr class="border-slate-100 my-1">
                                <form action="{{ route('folders.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Move folder and all contents to Trash?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 text-left">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Files Section -->
        <div>
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Files</h2>
            @if($documents->isEmpty())
                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-slate-500 font-medium">No files in this folder.</p>
                    <p class="text-slate-400 text-sm mt-1">Click "Upload Files" to add files.</p>
                </div>
            @else
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-6">Name</th>
                                    <th class="py-3 px-6">Category</th>
                                    <th class="py-3 px-6">Size</th>
                                    <th class="py-3 px-6">Last Modified</th>
                                    <th class="py-3 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($documents as $doc)
                                    <tr @contextmenu.stop.prevent="openContextMenu($event, 'file', {{ $doc->id }}, '{{ addslashes($doc->nama) }}')" class="hover:bg-slate-50/80 transition-colors text-sm text-slate-700 cursor-context-menu">
                                        <td class="py-4 px-6 font-semibold text-slate-800">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div class="truncate max-w-xs">
                                                    <a href="{{ route('documents.show', $doc->id) }}" class="hover:text-indigo-600 transition-colors">
                                                        {{ $doc->nama }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-400 block truncate">{{ $doc->nama_file_asli }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            @if($doc->documentCategory)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $doc->documentCategory->warna }}20; color: {{ $doc->documentCategory->warna }}">
                                                    {{ $doc->documentCategory->nama }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs">Uncategorized</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-slate-500 font-medium">
                                            {{ $doc->formattedSize() }}
                                        </td>
                                        <td class="py-4 px-6 text-slate-400">
                                            {{ $doc->updated_at->diffForHumans() }}
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex items-center justify-end space-x-2" x-data="{ open: false }">
                                                <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-all" title="Preview">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('documents.download', $doc->id) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-all" title="Download">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>

                                                <!-- Dropdown Actions -->
                                                <div class="relative">
                                                    <button @click="open = !open" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 focus:outline-none">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                                    </button>
                                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-xl py-1.5 z-40">
                                                        <a href="{{ route('documents.show', $doc->id) }}" class="w-full block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left">
                                                            Details & Sharing
                                                        </a>
                                                        <button @click="open = false; activeFileId = {{ $doc->id }}; activeFileName = '{{ $doc->nama }}'; showRenameFile = true" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left">
                                                            Rename
                                                        </button>
                                                        <button @click="open = false; activeFileId = {{ $doc->id }}; activeFileName = '{{ $doc->nama }}'; activeType = 'file'; showMove = true" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left">
                                                            Move to...
                                                        </button>
                                                        <hr class="border-slate-100 my-1">
                                                        <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Move document to Trash?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-full flex items-center px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 text-left">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Modals Section -->

        <!-- Create Folder Modal -->
        <div x-show="showCreateFolder" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="showCreateFolder = false">
                <h3 class="text-lg font-bold text-slate-800 mb-4">New Folder</h3>
                <form action="{{ route('folders.store') }}" method="POST">
                    @csrf
                    @if($folder)
                        <input type="hidden" name="parent_id" value="{{ $folder->id }}">
                    @endif
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Folder Name</label>
                            <input type="text" name="nama" required placeholder="Enter folder name..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showCreateFolder = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload Files Modal -->
        <div x-show="showUpload" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-data="{ selectedFiles: [] }">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg p-6 shadow-2xl" @click.away="showUpload = false; selectedFiles = []">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Upload Files</h3>
                <form action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($folder)
                        <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                    @endif
                    <div class="space-y-6">
                        <div class="border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-2xl p-8 flex flex-col items-center justify-center transition-colors cursor-pointer relative">
                            <input type="file" name="files[]" multiple required class="absolute inset-0 opacity-0 cursor-pointer" @change="selectedFiles = Array.from($event.target.files)">
                            <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </div>
                            <span class="text-slate-600 font-semibold text-sm" x-text="selectedFiles.length > 0 ? `${selectedFiles.length} file dipilih` : 'Drag and drop files here, or click to browse'">Drag and drop files here, or click to browse</span>
                            <span class="text-slate-400 text-xs mt-1">Maximum size: 200MB per file</span>
                        </div>

                        <!-- Selected Files List -->
                        <template x-if="selectedFiles.length > 0">
                            <div class="space-y-2 max-h-40 overflow-y-auto bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Daftar File Dipilih:</p>
                                <div class="space-y-1">
                                    <template x-for="file in selectedFiles" :key="file.name + file.size">
                                        <div class="flex items-center justify-between py-1 px-2 bg-white border border-slate-100 rounded-lg text-xs">
                                            <span class="font-medium text-slate-700 truncate max-w-[70%]" x-text="file.name"></span>
                                            <span class="text-slate-400" x-text="(file.size / 1024).toFixed(1) + ' KB'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showUpload = false; selectedFiles = []" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Upload</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rename Folder Modal -->
        <div x-show="showRenameFolder" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="showRenameFolder = false">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Rename Folder</h3>
                <form :action="`/folders/${activeFolderId}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Folder Name</label>
                            <input type="text" name="nama" required :value="activeFolderName" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showRenameFolder = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Rename</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rename File Modal -->
        <div x-show="showRenameFile" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="showRenameFile = false">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Rename File</h3>
                <form :action="`/documents/${activeFileId}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">File Name</label>
                            <input type="text" name="nama" required :value="activeFileName" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showRenameFile = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Rename</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Move Modal -->
        <div x-show="showMove" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="showMove = false">
                <h3 class="text-lg font-bold text-slate-800 mb-2">Move Item</h3>
                <p class="text-slate-400 text-xs mb-4">Select target destination folder</p>
                <form :action="activeType === 'folder' ? `/folders/${activeFolderId}/move` : `/documents/${activeFileId}/move`" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Destination Folder</label>
                            <select name="parent_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" x-init="$el.name = (activeType === 'folder') ? 'parent_id' : 'folder_id'">
                                <option value="">[My Drive - Root]</option>
                                <template x-for="f in allFolders">
                                    <option :value="f.id" x-text="f.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showMove = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-250 text-slate-700 text-sm font-semibold rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">Move</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Floating Upload Progress -->
        <div x-show="uploads.length > 0" x-transition class="fixed bottom-6 right-6 w-96 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-50 flex flex-col max-h-[400px]" x-cloak>
            <div class="bg-slate-800 text-white px-4 py-3 flex items-center justify-between shadow-sm cursor-pointer" @click="document.getElementById('uploadList').classList.toggle('hidden')">
                <h3 class="text-sm font-semibold flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Uploading <span class="mx-1" x-text="uploads.filter(u => u.status === 'uploading').length"></span> items
                </h3>
                <button @click.stop="clearUploads()" class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-slate-700 transition-colors" title="Batal & Tutup Semua">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="uploadList" class="overflow-y-auto flex-1 p-2 space-y-1">
                <template x-for="u in uploads" :key="u.id">
                    <div class="p-3 bg-white border border-slate-100 rounded-xl flex items-start justify-between space-x-3" :class="{'bg-rose-50/50': u.status === 'error', 'bg-emerald-50/30': u.status === 'completed'}">
                        <div class="flex items-start space-x-3 flex-1 min-w-0">
                            <div class="mt-1 shrink-0">
                                <template x-if="u.status === 'uploading'">
                                    <svg class="w-5 h-5 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                                <template x-if="u.status === 'completed'">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="u.status === 'error'">
                                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate" x-text="u.name"></p>
                                <template x-if="u.status === 'uploading'">
                                    <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" :style="`width: ${u.progress}%`"></div>
                                    </div>
                                </template>
                                <template x-if="u.status === 'error'">
                                    <p class="text-xs text-rose-500 mt-1 truncate" x-text="u.error"></p>
                                </template>
                            </div>
                        </div>
                        <template x-if="u.status === 'uploading'">
                            <button @click.stop="cancelUpload(u.id)" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors shrink-0" title="Batal Unggah">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Custom Right Click Context Menu -->
        <div x-show="contextMenu.show" 
             x-transition.opacity.duration.200ms
             class="fixed bg-white border border-slate-200 rounded-xl shadow-2xl py-1.5 z-[100] w-48 overflow-hidden"
             :style="`left: ${contextMenu.x}px; top: ${contextMenu.y}px;`"
             @click.stop
             x-cloak>
            
            <template x-if="contextMenu.type === 'folder'">
                <div>
                    <button @click="showRenameFolder = true; activeFolderId = contextMenu.id; activeFolderName = contextMenu.name; contextMenu.show = false" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Rename
                    </button>
                    <button @click="showMove = true; activeFolderId = contextMenu.id; activeFolderName = contextMenu.name; activeType = 'folder'; contextMenu.show = false" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Move to...
                    </button>
                    <hr class="border-slate-100 my-1">
                    <button @click="if(confirm('Move folder and all contents to Trash?')) { $refs.deleteFolderForm.action = `/folders/${contextMenu.id}`; $refs.deleteFolderForm.submit(); }" class="w-full flex items-center px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            </template>
            
            <template x-if="contextMenu.type === 'file'">
                <div>
                    <a :href="`/documents/${contextMenu.id}`" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Details & Sharing
                    </a>
                    <a :href="`/documents/${contextMenu.id}/preview`" target="_blank" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Preview
                    </a>
                    <a :href="`/documents/${contextMenu.id}/download`" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                    <hr class="border-slate-100 my-1">
                    <button @click="showRenameFile = true; activeFileId = contextMenu.id; activeFileName = contextMenu.name; contextMenu.show = false" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Rename
                    </button>
                    <button @click="showMove = true; activeFileId = contextMenu.id; activeFileName = contextMenu.name; activeType = 'file'; contextMenu.show = false" class="w-full flex items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Move to...
                    </button>
                    <hr class="border-slate-100 my-1">
                    <button @click="if(confirm('Move document to Trash?')) { $refs.deleteFileForm.action = `/documents/${contextMenu.id}`; $refs.deleteFileForm.submit(); }" class="w-full flex items-center px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 text-left transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            </template>
        </div>

        <form x-ref="deleteFolderForm" method="POST" class="hidden">@csrf @method('DELETE')</form>
        <form x-ref="deleteFileForm" method="POST" class="hidden">@csrf @method('DELETE')</form>
    </div>
</x-app-layout>
