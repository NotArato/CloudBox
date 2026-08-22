// Main CloudBox Dashboard & Storage Application Logic
let currentUser = null;
let currentFolderId = null;
let currentFolder = null;
let breadcrumbs = [];
let allFolders = [];
let folders = [];
let files = [];
let searchQuery = "";

// Helper: Format Bytes to MB/GB
function formatBytes(bytes, decimals = 2) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Initialize Application
document.addEventListener('DOMContentLoaded', async () => {
    try {
        currentUser = await Auth.requireAuth();
        updateUserUI();
        await loadData();
    } catch (e) {
        console.error('Init Error:', e);
    }
});

function updateUserUI() {
    if (!currentUser) return;
    const profile = currentUser.profile || {};

    const nameEls = document.querySelectorAll('.user-name');
    nameEls.forEach(el => el.textContent = profile.name || 'User');

    const emailEls = document.querySelectorAll('.user-email');
    emailEls.forEach(el => el.textContent = profile.email || '');

    const avatarEls = document.querySelectorAll('.user-avatar');
    avatarEls.forEach(el => el.textContent = (profile.name || 'U').charAt(0).toUpperCase());

    const planBadges = document.querySelectorAll('.plan-badge');
    planBadges.forEach(el => {
        if (profile.is_premium) {
            el.className = 'plan-badge px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-100 text-emerald-700 uppercase tracking-wider';
            el.textContent = 'PREMIUM';
        } else {
            el.className = 'plan-badge px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-600 uppercase tracking-wider';
            el.textContent = 'FREE PLAN';
        }
    });
}

async function loadData() {
    if (!currentUser) return;
    const userId = currentUser.id;

    // 1. Fetch all folders for select dropdowns & breadcrumbs
    const { data: folderList, error: fErr } = await window.supabaseClient
        .from('folders')
        .select('*')
        .eq('user_id', userId)
        .order('name');
    
    allFolders = folderList || [];

    // 2. Build Breadcrumbs for current folder
    breadcrumbs = [];
    if (currentFolderId) {
        let temp = allFolders.find(f => f.id === currentFolderId);
        currentFolder = temp || null;
        while (temp) {
            breadcrumbs.unshift(temp);
            temp = allFolders.find(f => f.id === temp.parent_id);
        }
    } else {
        currentFolder = null;
    }

    // 3. Filter folders for current directory
    folders = allFolders.filter(f => currentFolderId ? f.parent_id === currentFolderId : f.parent_id === null);

    // 4. Fetch files in current directory
    let fileQuery = window.supabaseClient
        .from('files')
        .select('*')
        .eq('user_id', userId)
        .order('created_at', { ascending: false });

    if (currentFolderId) {
        fileQuery = fileQuery.eq('folder_id', currentFolderId);
    } else {
        fileQuery = fileQuery.is('folder_id', null);
    }

    const { data: fileList, error: fileErr } = await fileQuery;
    files = fileList || [];

    // 5. Fetch total storage metrics across all files
    const { data: allFiles } = await window.supabaseClient
        .from('files')
        .select('size')
        .eq('user_id', userId);

    const totalUsedBytes = (allFiles || []).reduce((sum, item) => sum + (Number(item.size) || 0), 0);
    const limitBytes = currentUser.profile.is_premium ? 5368709120 : 1073741824; // 5GB vs 1GB
    const percentage = Math.min(100, Math.round((totalUsedBytes / limitBytes) * 100));

    // Update Storage Bar UI
    const usedText = document.getElementById('storageUsedText');
    if (usedText) usedText.textContent = `${formatBytes(totalUsedBytes)} of ${formatBytes(limitBytes)} used`;

    const barEl = document.getElementById('storageProgressBar');
    if (barEl) barEl.style.width = `${percentage}%`;

    const percentText = document.getElementById('storagePercentageText');
    if (percentText) percentText.textContent = `${percentage}%`;

    // Render Views
    renderBreadcrumbs();
    renderFolders();
    renderFiles();
    updateFolderDropdowns();
}

function renderBreadcrumbs() {
    const container = document.getElementById('breadcrumbContainer');
    if (!container) return;

    let html = `
        <a href="javascript:void(0)" onclick="navigateToFolder(null)" class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition shrink-0">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Root Storage
        </a>
    `;

    breadcrumbs.forEach(crumb => {
        html += `
            <span class="text-slate-300">/</span>
            <a href="javascript:void(0)" onclick="navigateToFolder('${crumb.id}')" class="text-slate-900 hover:text-indigo-600 font-semibold transition truncate max-w-[150px]">
                ${escapeHtml(crumb.name)}
            </a>
        `;
    });

    container.innerHTML = html;
}

function renderFolders() {
    const container = document.getElementById('foldersContainer');
    const countEl = document.getElementById('folderCount');
    if (!container) return;

    const filtered = folders.filter(f => f.name.toLowerCase().includes(searchQuery.toLowerCase()));
    if (countEl) countEl.textContent = `Folders (${filtered.length})`;

    if (filtered.length === 0) {
        container.innerHTML = `<div class="col-span-full text-xs text-slate-400 italic mb-6">No folders in this directory.</div>`;
        return;
    }

    let html = '';
    filtered.forEach(folder => {
        html += `
            <div class="clean-card p-4 rounded-2xl border border-slate-200/90 hover:border-cyan-500/40 transition group flex items-center justify-between">
                <a href="javascript:void(0)" onclick="navigateToFolder('${folder.id}')" class="flex items-center gap-3.5 min-w-0 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold font-heading text-slate-900 truncate group-hover:text-cyan-600 transition">${escapeHtml(folder.name)}</h4>
                        <span class="text-[11px] text-slate-400 font-mono">Folder</span>
                    </div>
                </a>
                <div class="flex items-center gap-1 opacity-70 group-hover:opacity-100 transition">
                    <button onclick="openRenameFolderModal('${folder.id}', '${escapeHtml(folder.name)}')" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition" title="Rename Folder">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button onclick="deleteFolder('${folder.id}')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Delete Folder">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function renderFiles() {
    const container = document.getElementById('filesTableBody');
    const countEl = document.getElementById('fileCount');
    if (!container) return;

    const filtered = files.filter(f => f.name.toLowerCase().includes(searchQuery.toLowerCase()) || f.original_name.toLowerCase().includes(searchQuery.toLowerCase()));
    if (countEl) countEl.textContent = `Files (${filtered.length})`;

    if (filtered.length === 0) {
        container.innerHTML = `
            <tr>
                <td colspan="5" class="p-12 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto text-slate-400 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">No files found</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    filtered.forEach(file => {
        const dateStr = new Date(file.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const ext = file.original_name.split('.').pop() || 'file';

        html += `
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-6 py-4 font-medium text-slate-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-900 truncate max-w-xs">${escapeHtml(file.name)}</div>
                        <div class="text-[11px] text-slate-400 font-mono truncate max-w-xs">${escapeHtml(file.original_name)}</div>
                    </div>
                </td>
                <td class="px-6 py-4 text-xs font-mono text-slate-500">${formatBytes(file.size)}</td>
                <td class="px-6 py-4 text-xs font-mono text-slate-500 uppercase">${escapeHtml(ext)}</td>
                <td class="px-6 py-4 text-xs text-slate-500">${dateStr}</td>
                <td class="px-6 py-4 align-middle">
                    <div class="flex items-center justify-end gap-1.5">
                        <button onclick="downloadFile('${file.storage_path}', '${escapeHtml(file.original_name)}')" class="w-8 h-8 flex items-center justify-center text-indigo-600 hover:bg-indigo-50 rounded-xl transition" title="Download">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <button onclick="openRenameFileModal('${file.id}', '${escapeHtml(file.name)}')" class="w-8 h-8 flex items-center justify-center text-cyan-600 hover:bg-cyan-50 rounded-xl transition" title="Rename">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button onclick="openMoveFileModal('${file.id}')" class="w-8 h-8 flex items-center justify-center text-amber-600 hover:bg-amber-50 rounded-xl transition" title="Move File">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </button>
                        <button onclick="deleteFile('${file.id}', '${file.storage_path}')" class="w-8 h-8 flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    container.innerHTML = html;
}

function updateFolderDropdowns() {
    const moveSelect = document.getElementById('moveFolderSelect');
    if (!moveSelect) return;

    let html = `<option value="">Root Storage</option>`;
    allFolders.forEach(f => {
        html += `<option value="${f.id}">${escapeHtml(f.name)}</option>`;
    });

    moveSelect.innerHTML = html;
}

// Navigation Actions
function navigateToFolder(id) {
    currentFolderId = id;
    loadData();
}

function handleSearch(query) {
    searchQuery = query;
    renderFolders();
    renderFiles();
}

// Modal Helpers
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('hidden');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

// Actions
async function createFolder(e) {
    e.preventDefault();
    const input = document.getElementById('folderNameInput');
    const name = input.value.trim();
    if (!name) return;

    const { error } = await window.supabaseClient.from('folders').insert({
        user_id: currentUser.id,
        parent_id: currentFolderId || null,
        name: name
    });

    if (error) {
        alert('Error creating folder: ' + error.message);
        return;
    }

    input.value = '';
    closeModal('newFolderModal');
    loadData();
}

async function uploadFileSubmit(e) {
    e.preventDefault();
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    if (!file) return;

    // Check Single File Limit
    const isPremium = currentUser.profile.is_premium;
    const maxSingleMb = isPremium ? 100 : 10;
    if (file.size > maxSingleMb * 1024 * 1024) {
        alert(`File size exceeds your ${isPremium ? 'Premium' : 'Free'} plan limit of ${maxSingleMb} MB per file.`);
        return;
    }

    const btn = document.getElementById('uploadSubmitBtn');
    if (btn) btn.disabled = true;

    try {
        await StorageHandler.uploadFile(currentUser.id, file, currentFolderId);
        fileInput.value = '';
        closeModal('uploadFileModal');
        await loadData();
    } catch (err) {
        alert('Upload Error: ' + err.message);
    } finally {
        if (btn) btn.disabled = false;
    }
}

async function deleteFolder(folderId) {
    if (!confirm('Are you sure you want to delete this folder and all contents inside?')) return;

    const { error } = await window.supabaseClient.from('folders').delete().eq('id', folderId);
    if (error) {
        alert('Error deleting folder: ' + error.message);
        return;
    }
    loadData();
}

async function deleteFile(fileId, storagePath) {
    if (!confirm('Are you sure you want to delete this file?')) return;

    try {
        await StorageHandler.deleteFile(fileId, storagePath);
        loadData();
    } catch (err) {
        alert('Error deleting file: ' + err.message);
    }
}

function downloadFile(storagePath, originalName) {
    StorageHandler.downloadFile(storagePath, originalName);
}

function openRenameFolderModal(id, currentName) {
    document.getElementById('renameFolderId').value = id;
    document.getElementById('renameFolderNameInput').value = currentName;
    openModal('renameFolderModal');
}

async function submitRenameFolder(e) {
    e.preventDefault();
    const id = document.getElementById('renameFolderId').value;
    const newName = document.getElementById('renameFolderNameInput').value.trim();

    if (!newName) return;

    const { error } = await window.supabaseClient.from('folders').update({ name: newName }).eq('id', id);
    if (error) alert(error.message);

    closeModal('renameFolderModal');
    loadData();
}

function openRenameFileModal(id, currentName) {
    document.getElementById('renameFileId').value = id;
    document.getElementById('renameFileNameInput').value = currentName;
    openModal('renameFileModal');
}

async function submitRenameFile(e) {
    e.preventDefault();
    const id = document.getElementById('renameFileId').value;
    const newName = document.getElementById('renameFileNameInput').value.trim();

    if (!newName) return;

    const { error } = await window.supabaseClient.from('files').update({ name: newName }).eq('id', id);
    if (error) alert(error.message);

    closeModal('renameFileModal');
    loadData();
}

function openMoveFileModal(id) {
    document.getElementById('moveFileId').value = id;
    openModal('moveFileModal');
}

async function submitMoveFile(e) {
    e.preventDefault();
    const fileId = document.getElementById('moveFileId').value;
    const targetFolderId = document.getElementById('moveFolderSelect').value || null;

    const { error } = await window.supabaseClient.from('files').update({ folder_id: targetFolderId }).eq('id', fileId);
    if (error) alert(error.message);

    closeModal('moveFileModal');
    loadData();
}

function escapeHtml(str) {
    return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
