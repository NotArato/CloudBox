<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $totalFiles = $user->files()->count();
        $totalFolders = $user->folders()->count();
        $storageUsed = $user->getStorageUsed();
        $storageLimit = $user->storage_limit;
        $storagePercentage = $user->getStoragePercentage();

        $recentFiles = $user->files()
            ->with('folder')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'totalFiles',
            'totalFolders',
            'storageUsed',
            'storageLimit',
            'storagePercentage',
            'recentFiles'
        ));
    }
}
