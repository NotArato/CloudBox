<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:folders,id'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->parent_id) {
            $parentFolder = Folder::where('user_id', $user->id)->findOrFail($request->parent_id);
        }

        $user->folders()->create([
            'name' => trim($request->name),
            'parent_id' => $request->parent_id ?: null,
        ]);

        return back()->with('success', 'Folder created successfully!');
    }

    public function update(Request $request, Folder $folder)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($folder->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $folder->update([
            'name' => trim($request->name),
        ]);

        return back()->with('success', 'Folder renamed successfully!');
    }

    public function destroy(Folder $folder)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($folder->user_id !== $user->id) {
            abort(403);
        }

        //  remove files from disk
        $this->deleteFolderContents($folder);

        $folder->delete();

        return back()->with('success', 'Folder deleted successfully!');
    }

    private function deleteFolderContents(Folder $folder)
    {
        // Delete all files in this folder
        foreach ($folder->files as $file) {
            if (Storage::disk('public')->exists($file->storage_path)) {
                Storage::disk('public')->delete($file->storage_path);
            }
            $file->delete();
        }

        //  delete subfolders
        foreach ($folder->children as $childFolder) {
            $this->deleteFolderContents($childFolder);
            $childFolder->delete();
        }
    }
}
