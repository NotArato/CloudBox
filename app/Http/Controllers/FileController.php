<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $currentFolderId = $request->query('folder_id');
        $currentFolder = null;
        $breadcrumbs = [];

        if ($currentFolderId) {
            $currentFolder = Folder::where('user_id', $user->id)->findOrFail($currentFolderId);
            
            $temp = $currentFolder;
            while ($temp) {
                array_unshift($breadcrumbs, $temp);
                $temp = $temp->parent;
            }
        }

        // Fetch subfolders in current directory
        $folders = Folder::where('user_id', $user->id)
            ->where('parent_id', $currentFolderId)
            ->orderBy('name')
            ->get();

        // Fetch files in current directory
        $files = File::where('user_id', $user->id)
            ->where('folder_id', $currentFolderId)
            ->orderBy('created_at', 'desc')
            ->get();

        $allFolders = Folder::where('user_id', $user->id)->orderBy('name')->get();

        return view('files.index', compact(
            'user',
            'currentFolder',
            'breadcrumbs',
            'folders',
            'files',
            'allFolders'
        ));
    }

    public function upload(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'file' => ['required', 'file'],
            'folder_id' => ['nullable', 'exists:folders,id'],
        ]);

        $uploadedFile = $request->file('file');
        $fileSize = $uploadedFile->getSize();

        // 1. Single File Upload Limit Check
        $maxSingleLimit = $user->getMaxFileUploadLimit(); // 10MB or 100MB
        if ($fileSize > $maxSingleLimit) {
            $maxMb = round($maxSingleLimit / (1024 * 1024));
            $userType = $user->is_premium ? 'Premium' : 'Free';
            return back()->withErrors([
                'file' => "File size exceeds your {$userType} plan limit of {$maxMb} MB per file. Please upgrade or upload a smaller file."
            ]);
        }

        // 2. Total Storage Usage Quota Check
        $currentUsed = $user->getStorageUsed();
        $limit = $user->storage_limit;

        if (($currentUsed + $fileSize) > $limit) {
            $limitMb = round($limit / (1024 * 1024));
            return back()->withErrors([
                'file' => "Storage capacity limit of {$limitMb} MB reached! Please upgrade to Premium for 5 GB storage capacity."
            ]);
        }

        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $storedFileName = Str::uuid()->toString() . ($extension ? '.' . $extension : '');
        $storageDir = "user_files/{$user->id}";

        $path = $uploadedFile->storeAs($storageDir, $storedFileName, 'public');

        // To create a new file record in the database

        File::create([
            'user_id' => $user->id,
            'folder_id' => $request->folder_id ?: null,
            'name' => pathinfo($originalName, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'storage_path' => $path,
            'mime_type' => $uploadedFile->getClientMimeType(),
            'size' => $fileSize,
        ]);

        return back()->with('success', "File '{$originalName}' uploaded successfully!");
    }

    public function download(File $file)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($file->user_id !== $user->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->storage_path)) {
            return back()->withErrors(['error' => 'File not found on disk.']);
        }

        return Storage::disk('public')->download($file->storage_path, $file->original_name);
    }

    public function rename(Request $request, File $file)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($file->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $file->update([
            'name' => trim($request->name),
        ]);

        return back()->with('success', 'File renamed successfully!');
    }

    public function move(Request $request, File $file)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($file->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'folder_id' => ['nullable', 'exists:folders,id'],
        ]);

        if ($request->folder_id) {
            $targetFolder = Folder::where('user_id', $user->id)->findOrFail($request->folder_id);
        }

        $file->update([
            'folder_id' => $request->folder_id ?: null,
        ]);

        return back()->with('success', 'File moved successfully!');
    }

    public function destroy(File $file)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($file->user_id !== $user->id) {
            abort(403);
        }

        if (Storage::disk('public')->exists($file->storage_path)) {
            Storage::disk('public')->delete($file->storage_path);
        }

        $file->delete();

        return back()->with('success', 'File deleted successfully!');
    }
}
