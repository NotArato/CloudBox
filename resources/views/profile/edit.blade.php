@extends('layouts.app')

@section('title', 'Profile Settings - CloudBox')
@section('page-title', 'Account & Profile Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <!-- Profile Header Card -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="flex items-center gap-5 z-10">
            <div class="w-20 h-20 rounded-3xl bg-indigo-600 flex items-center justify-center text-3xl font-extrabold font-heading text-white shadow-lg shadow-indigo-600/20 shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="space-y-1 text-center sm:text-left">
                <h2 class="text-2xl font-bold font-heading text-slate-900">{{ $user->name }}</h2>
                <p class="text-xs font-mono text-slate-500">{{ $user->email }}</p>
                <div class="pt-1.5 flex flex-wrap gap-2 justify-center sm:justify-start">
                    @if($user->is_premium)
                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-300 text-[11px] font-bold">
                            Premium User
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-semibold">
                            Free User
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full bg-slate-50 text-slate-500 border border-slate-200 text-[11px]">
                        Joined {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    <!-- Forms Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Form 1: Personal Information -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-xs space-y-6">
            <div>
                <h3 class="text-lg font-bold font-heading text-slate-900">Personal Information</h3>
                <p class="text-xs text-slate-500 mt-1">Update your account's display name and email address.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-slate-900 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-slate-900 text-sm">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition shadow-md shadow-indigo-600/20">
                        Save Profile Details
                    </button>
                </div>
            </form>
        </div>

        <!-- Form 2: Change Password -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-xs space-y-6">
            <div>
                <h3 class="text-lg font-bold font-heading text-slate-900">Update Password</h3>
                <p class="text-xs text-slate-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-slate-900 text-sm" placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">New Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-slate-900 text-sm" placeholder="••••••••">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs border border-slate-200 transition">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
