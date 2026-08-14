@extends('layouts.app')

@section('title', 'Register - CloudBox')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-md p-8 bg-white rounded-3xl shadow-xl border border-slate-200 text-slate-900 relative overflow-hidden">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold font-heading text-slate-900">Create CloudBox Account</h2>
            <p class="text-xs text-slate-500 mt-1">Get 100 MB free cloud storage instantly</p>
        </div>

        <form id="registerForm" method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-slate-900 text-sm placeholder-slate-400 transition"
                    placeholder="John Doe">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-slate-900 text-sm placeholder-slate-400 transition"
                    placeholder="user@example.com">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-slate-900 text-sm placeholder-slate-400 transition"
                    placeholder="••••••••">
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition shadow-md shadow-indigo-600/20 mt-2">
                Register Free Account
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-500 border-t border-slate-100 pt-6">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold ml-1">Sign in here</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.querySelectorAll('input').forEach(function (input) {
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (registerForm.checkValidity()) {
                            registerForm.submit();
                        } else {
                            registerForm.reportValidity();
                        }
                    }
                });
            });
        }
    });
</script>
@endsection
