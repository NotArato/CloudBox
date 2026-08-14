@extends('layouts.app')

@section('title', 'Dashboard - CloudBox')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Files -->
        <div class="clean-card p-6 rounded-3xl flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Files</span>
                <h3 class="text-3xl font-extrabold font-heading text-slate-900">{{ number_format($totalFiles) }}</h3>
                <span class="text-xs text-indigo-600 font-medium">Stored in cloud</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <!-- Total Folders -->
        <div class="clean-card p-6 rounded-3xl flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Folders</span>
                <h3 class="text-3xl font-extrabold font-heading text-slate-900">{{ number_format($totalFolders) }}</h3>
                <span class="text-xs text-cyan-600 font-medium">Organized directories</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                </svg>
            </div>
        </div>

        <!-- Storage Used -->
        @php
            $usedMb = number_format($storageUsed / (1024 * 1024), 2);
            $limitText = $user->is_premium ? '5 GB' : '100 MB';
        @endphp
        <div class="clean-card p-6 rounded-3xl flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Storage Used</span>
                <h3 class="text-3xl font-extrabold font-heading text-slate-900">{{ $usedMb }} <span class="text-xs font-normal text-slate-500">MB</span></h3>
                <span class="text-xs text-amber-600 font-medium">{{ $storagePercentage }}% of {{ $limitText }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s-8-1.79-8-4"></path>
                </svg>
            </div>
        </div>

        <!-- Account Plan -->
        <div class="clean-card p-6 rounded-3xl flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Account Status</span>
                <h3 class="text-2xl font-extrabold font-heading text-slate-900">
                    @if($user->is_premium)
                        <span class="text-amber-600">Premium User</span>
                    @else
                        <span class="text-slate-700">Free Plan</span>
                    @endif
                </h3>
                <span class="text-xs text-slate-500">
                    @if($user->is_premium)
                        Max 100 MB / file
                    @else
                        Max 10 MB / file
                    @endif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl {{ $user->is_premium ? 'bg-amber-100 border border-amber-200 text-amber-700' : 'bg-slate-100 border border-slate-200 text-slate-600' }} flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Recent Uploads Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold font-heading text-slate-900">Recent Uploads</h3>
                <p class="text-xs text-slate-500">Files recently saved to your storage</p>
            </div>
            <a href="{{ route('files.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1.5 transition">
                View All Files
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        @if($recentFiles->isEmpty())
            <div class="p-16 text-center text-slate-400 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-500">No files uploaded yet</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/70 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">File Name</th>
                            <th class="px-6 py-4">Folder</th>
                            <th class="px-6 py-4">Size</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentFiles as $file)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 font-medium text-slate-900 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <span class="truncate max-w-xs font-medium text-slate-900">{{ $file->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    @if($file->folder)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200/80">{{ $file->folder->name }}</span>
                                    @else
                                        <span class="text-slate-400 font-mono">Root Directory</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $file->formatted_size }}</td>
                                <td class="px-6 py-4 text-xs text-slate-500">{{ $file->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex items-center justify-end">
                                        <a href="{{ route('files.download', $file) }}" class="w-8 h-8 flex items-center justify-center text-indigo-600 hover:bg-indigo-50 rounded-xl transition shrink-0" title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </a>
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
@endsection
