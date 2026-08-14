@extends('layouts.app')

@section('title', 'My Storage - CloudBox')
@section('page-title', 'File & Folder Storage')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Top Action Bar & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-sm font-medium overflow-x-auto py-1">
            <a href="{{ route('files.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition shrink-0">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Root Storage
            </a>

            @foreach($breadcrumbs as $crumb)
                <span class="text-slate-300">/</span>
                <a href="{{ route('files.index', ['folder_id' => $crumb->id]) }}" class="text-slate-900 hover:text-indigo-600 font-semibold transition truncate max-w-[150px]">
                    {{ $crumb->name }}
                </a>
            @endforeach
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <button onclick="openModal('newFolderModal')" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold flex items-center gap-2 transition border border-slate-200 shadow-xs">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
                New Folder
            </button>

            <button onclick="openModal('uploadFileModal')" class="px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 transition shadow-md shadow-indigo-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"></path>
                </svg>
                Upload File
            </button>
        </div>
    </div>

    <!-- Folders Section -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Folders ({{ $folders->count() }})</h3>
        </div>

        @if($folders->isEmpty())
            <div class="text-xs text-slate-400 italic mb-6">No subfolders inside this directory.</div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                @foreach($folders as $folder)
                    <div class="clean-card p-4 rounded-2xl border border-slate-200/90 hover:border-cyan-500/40 transition group flex items-center justify-between">
                        <a href="{{ route('files.index', ['folder_id' => $folder->id]) }}" class="flex items-center gap-3.5 min-w-0 flex-1">
                            <div class="w-11 h-11 rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold font-heading text-slate-900 truncate group-hover:text-cyan-600 transition">{{ $folder->name }}</h4>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $folder->files()->count() }} files</span>
                            </div>
                        </a>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 opacity-70 group-hover:opacity-100 transition">
                            <button onclick="openRenameFolderModal({{ $folder->id }}, '{{ addslashes($folder->name) }}')" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition" title="Rename Folder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('folders.destroy', $folder) }}" onsubmit="return confirm('Delete this folder and all contents?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Delete Folder">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Files Table Section -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Files ({{ $files->count() }})</h3>
        </div>

        @if($files->isEmpty())
            <div class="p-16 text-center text-slate-400 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-500">No files in this folder</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/70 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">File Name</th>
                            <th class="px-6 py-4">Size</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Date Added</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($files as $file)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 font-medium text-slate-900 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        @if($file->icon_category === 'image')
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @elseif($file->icon_category === 'pdf')
                                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 truncate max-w-xs">{{ $file->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono truncate max-w-xs">{{ $file->original_name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $file->formatted_size }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-500 uppercase">{{ pathinfo($file->original_name, PATHINFO_EXTENSION) ?: 'File' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-500">{{ $file->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Download -->
                                        <a href="{{ route('files.download', $file) }}" class="w-8 h-8 flex items-center justify-center text-indigo-600 hover:bg-indigo-50 rounded-xl transition shrink-0" title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </a>

                                        <!-- Rename -->
                                        <button onclick="openRenameFileModal({{ $file->id }}, '{{ addslashes($file->name) }}')" class="w-8 h-8 flex items-center justify-center text-cyan-600 hover:bg-cyan-50 rounded-xl transition shrink-0" title="Rename">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <!-- Move -->
                                        <button onclick="openMoveFileModal({{ $file->id }}, {{ $file->folder_id ?: 'null' }})" class="w-8 h-8 flex items-center justify-center text-amber-600 hover:bg-amber-50 rounded-xl transition shrink-0" title="Move File">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        </button>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('files.destroy', $file) }}" onsubmit="return confirm('Are you sure you want to delete this file?');" class="flex items-center shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- New Folder Modal -->
<div id="newFolderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="w-full max-w-md p-6 bg-white rounded-3xl shadow-2xl border border-slate-200 text-slate-900">
        <h3 class="text-xl font-bold font-heading text-slate-900 mb-4">Create New Folder</h3>
        <form method="POST" action="{{ route('folders.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="parent_id" value="{{ optional($currentFolder)->id }}">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Folder Name</label>
                <input type="text" name="name" required placeholder="e.g. Work Documents"
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 text-slate-900 text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('newFolderModal')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold shadow-md shadow-cyan-600/20">Create Folder</button>
            </div>
        </form>
    </div>
</div>

<!-- Upload File Modal -->
<div id="uploadFileModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="w-full max-w-md p-6 bg-white rounded-3xl shadow-2xl border border-slate-200 text-slate-900">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold font-heading text-slate-900">Upload File</h3>
            <span class="text-xs px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono">
                Max {{ $user->is_premium ? '100 MB' : '10 MB' }}
            </span>
        </div>
        <form method="POST" action="{{ route('files.upload') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="folder_id" value="{{ optional($currentFolder)->id }}">
            
            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-indigo-500 transition cursor-pointer bg-slate-50">
                <svg class="w-10 h-10 mx-auto text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <input type="file" name="file" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
            </div>

            @if(!$user->is_premium)
                <p class="text-xs text-amber-800 bg-amber-50 p-3 rounded-2xl border border-amber-200">
                    💡 Free Account limit is 100 MB total. Upgrade to PRO for 5 GB capacity!
                </p>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('uploadFileModal')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-md shadow-indigo-600/20">Upload Now</button>
            </div>
        </form>
    </div>
</div>

<!-- Rename Folder Modal -->
<div id="renameFolderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="w-full max-w-md p-6 bg-white rounded-3xl shadow-2xl border border-slate-200 text-slate-900">
        <h3 class="text-xl font-bold font-heading text-slate-900 mb-4">Rename Folder</h3>
        <form id="renameFolderForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">New Folder Name</label>
                <input type="text" id="renameFolderNameInput" name="name" required
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-cyan-600 text-slate-900 text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('renameFolderModal')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold">Save Name</button>
            </div>
        </form>
    </div>
</div>

<!-- Rename File Modal -->
<div id="renameFileModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="w-full max-w-md p-6 bg-white rounded-3xl shadow-2xl border border-slate-200 text-slate-900">
        <h3 class="text-xl font-bold font-heading text-slate-900 mb-4">Rename File</h3>
        <form id="renameFileForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">New File Name</label>
                <input type="text" id="renameFileInput" name="name" required
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 text-slate-900 text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('renameFileModal')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">Save Name</button>
            </div>
        </form>
    </div>
</div>

<!-- Move File Modal -->
<div id="moveFileModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="w-full max-w-md p-6 bg-white rounded-3xl shadow-2xl border border-slate-200 text-slate-900">
        <h3 class="text-xl font-bold font-heading text-slate-900 mb-4">Move File to Folder</h3>
        <form id="moveFileForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Select Target Directory</label>
                <select id="moveFileSelect" name="folder_id" class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-amber-600 text-slate-900 text-sm">
                    <option value="">📂 Root Storage</option>
                    @foreach($allFolders as $f)
                        <option value="{{ $f->id }}">📁 {{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('moveFileModal')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold">Move File</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openRenameFolderModal(id, currentName) {
        document.getElementById('renameFolderForm').action = "/folders/" + id;
        document.getElementById('renameFolderNameInput').value = currentName;
        openModal('renameFolderModal');
    }

    function openRenameFileModal(id, currentName) {
        document.getElementById('renameFileForm').action = "/files/" + id + "/rename";
        document.getElementById('renameFileInput').value = currentName;
        openModal('renameFileModal');
    }

    function openMoveFileModal(id, currentFolderId) {
        document.getElementById('moveFileForm').action = "/files/" + id + "/move";
        document.getElementById('moveFileSelect').value = currentFolderId || "";
        openModal('moveFileModal');
    }
</script>
@endsection
