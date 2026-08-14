<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CloudBox - Next-Gen Cloud Storage')</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite / Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
            color: #0f172a;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        .clean-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        }
        .clean-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .clean-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(99, 102, 241, 0.12);
        }
        .aba-header-bg {
            background: linear-gradient(135deg, #002b49 0%, #004d80 100%);
        }
        /* Custom Clean Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="h-full flex bg-slate-50 text-slate-900 antialiased overflow-x-hidden">

    @auth
    <!-- Clean Minimalist White Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200/90 flex flex-col justify-between hidden md:flex shrink-0 z-20 shadow-sm">
        <div>
            <!-- Brand Header -->
            <div class="h-20 flex items-center px-6 border-b border-slate-100 gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight font-heading bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-800 bg-clip-text text-transparent block">CloudBox</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-indigo-600 block -mt-1">Cloud Storage</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1">
                <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Main Menu</div>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600 border border-indigo-200/80 font-semibold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('files.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('files.*') ? 'bg-indigo-50 text-indigo-600 border border-indigo-200/80 font-semibold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('files.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    My Storage
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-indigo-600 border border-indigo-200/80 font-semibold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                </a>

                <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Subscription</div>

                <a href="{{ route('upgrade') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('upgrade') ? 'bg-amber-50 text-amber-700 border border-amber-200 font-semibold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Upgrade Plan
                    </div>
                    @if(Auth::user()->is_premium)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-amber-100 text-amber-800 border border-amber-300">PREMIUM</span>
                    @else
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 border border-slate-200">FREE</span>
                    @endif
                </a>
            </nav>
        </div>

        <!-- Sidebar Storage Mini Meter -->
        <div class="p-4 border-t border-slate-100">
            @php
                /** @var \App\Models\User $u */
                $u = Auth::user();
                $usedBytes = $u->getStorageUsed();
                $limitBytes = $u->storage_limit;
                $pct = $u->getStoragePercentage();
                $usedMb = number_format($usedBytes / (1024 * 1024), 1);
                $limitMbOrGb = $u->is_premium ? '5 GB' : '100 MB';
            @endphp
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-2.5">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-600 font-medium">Storage Used</span>
                    <span class="text-indigo-600 font-bold">{{ $pct }}%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-indigo-600 to-cyan-500 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
                <div class="text-[11px] text-slate-500 flex justify-between font-mono">
                    <span>{{ $usedMb }} MB</span>
                    <span>{{ $limitMbOrGb }}</span>
                </div>

                @if(!$u->is_premium)
                <a href="{{ route('upgrade') }}" class="block text-center mt-3 w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow-md shadow-indigo-600/20">
                    Upgrade to 5 GB
                </a>
                @endif
            </div>
        </div>
    </aside>
    @endauth

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        @auth
        <!-- Top White Navbar -->
        <header class="h-20 bg-white/90 border-b border-slate-200/80 flex items-center justify-between px-8 backdrop-blur-md sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-4">
                <!-- Mobile Toggle -->
                <button onclick="document.querySelector('aside').classList.toggle('hidden')" class="md:hidden p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <h1 class="text-xl font-bold font-heading text-slate-900">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <!-- Header Right Menu -->
            <div class="flex items-center gap-4">
                @if(Auth::user()->is_premium)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-300 text-xs font-bold tracking-wide shadow-xs">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        PREMIUM USER
                    </div>
                @endif

                <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-1 rounded-2xl hover:bg-slate-100 transition group" title="Click to customize profile">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-sm font-bold text-white shadow-md shadow-indigo-600/20 group-hover:scale-105 transition">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-slate-900 leading-snug group-hover:text-indigo-600 transition">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-500 leading-none">{{ Auth::user()->email }}</div>
                        </div>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-xl transition" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>
        @endauth

        <!-- Alerts -->
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 shadow-xs">
                    @foreach($errors->all() as $error)
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Main Body -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
