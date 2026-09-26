<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Dashboard') - REHAT-PW STIKes Panti Waluya Malang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        html, body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0B1120;
            max-width: 100vw;
            overflow-x: hidden;
        }
        .font-display {
            font-family: 'Outfit', sans-serif;
        }
        select {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-950 text-slate-100 antialiased max-w-full overflow-x-hidden selection:bg-blue-600 selection:text-white">

    <!-- Top Ambient Glow Line -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-gradient-to-tr from-blue-600/15 via-indigo-600/15 to-purple-600/10 blur-[120px] rounded-full"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 glass-panel border-b border-slate-800/90 shadow-2xl transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between py-3 md:py-0 min-h-[4.5rem] gap-2 md:gap-0">
                
                <!-- Branding & Mobile Toggle -->
                <div class="flex items-center justify-between w-full md:w-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl blur-sm opacity-50 group-hover:opacity-100 transition duration-300"></div>
                            <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes Panti Waluya" class="relative h-10 sm:h-11 w-auto object-contain drop-shadow group-hover:scale-105 transition-transform shrink-0">
                        </div>
                        <div class="leading-tight">
                            <div class="flex items-center gap-2">
                                <span class="font-display text-xl sm:text-2xl font-black tracking-tight text-white group-hover:text-blue-400 transition-colors">REHAT-PW</span>
                                <span class="inline-flex items-center gap-1 text-[9px] uppercase font-bold text-blue-400 bg-blue-500/15 px-2 py-0.5 rounded-full border border-blue-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                                    INTERNAL
                                </span>
                            </div>
                            <div class="text-[11px] sm:text-xs text-slate-400 font-medium truncate max-w-[200px] sm:max-w-none">STIKes Panti Waluya Malang</div>
                        </div>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="toggleAppMobileMenu()" class="md:hidden p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 focus:outline-none transition-colors shrink-0" aria-label="Toggle Navigation">
                        <i data-lucide="menu" id="appMenuOpenIcon" class="w-5 h-5"></i>
                        <i data-lucide="x" id="appMenuCloseIcon" class="w-5 h-5 hidden"></i>
                    </button>
                </div>

                <!-- Nav Links & User Info -->
                <div id="appNavMenu" class="hidden md:flex flex-col md:flex-row items-stretch md:items-center gap-2 sm:gap-3 pt-2 md:pt-0 border-t md:border-t-0 border-slate-800 text-xs sm:text-sm">
                    <nav class="flex flex-col md:flex-row items-stretch md:items-center gap-1 sm:gap-1.5">
                        <a href="{{ route('dashboard') }}" 
                           class="px-3.5 py-2 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold border border-blue-500' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </a>

                        @if($isLemburEnabled ?? false)
                            <a href="{{ route('lembur.index') }}" 
                               class="px-3.5 py-2 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('lembur.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30 font-extrabold border border-amber-400' : 'text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20' }}">
                                <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
                                <span>Klaim Lembur</span>
                            </a>
                        @endif

                        <a href="{{ route('arsip.index') }}" 
                           class="px-3.5 py-2 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('arsip.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold border border-blue-500' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="archive" class="w-4 h-4"></i>
                            <span>Arsip Tahunan</span>
                        </a>

                        <a href="{{ route('reports.index') }}" 
                           class="px-3.5 py-2 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold border border-blue-500' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            <span>Laporan &amp; Rekap</span>
                        </a>

                        @if(Auth::user()->isHrd())
                            <a href="{{ route('pegawai.index') }}" 
                               class="px-3.5 py-2 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('pegawai.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold border border-blue-500' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <i data-lucide="users" class="w-4 h-4"></i>
                                <span>Pegawai</span>
                            </a>
                            <a href="{{ route('divisi.index') }}" 
                               class="px-3.5 py-2 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('divisi.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold border border-blue-500' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                <span>Divisi/Prodi</span>
                            </a>
                            <a href="{{ route('users.index') }}" 
                               class="px-3.5 py-2 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold border border-blue-500' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <i data-lucide="user-cog" class="w-4 h-4"></i>
                                <span>User Akun</span>
                            </a>
                        @endif
                    </nav>

                    <!-- User Account Pill & Logout -->
                    <div class="flex items-center justify-between md:justify-start gap-3 border-t md:border-t-0 md:border-l border-slate-800 pt-2 md:pt-0 md:pl-4">
                        <div class="text-left leading-tight">
                            <div class="font-display font-bold text-white text-xs sm:text-sm">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-teal-400 font-bold uppercase tracking-wider">
                                @if(Auth::user()->isKadiv())
                                    Kadiv {{ Auth::user()->divisi->nama_divisi ?? '' }}
                                @elseif(Auth::user()->isHrd())
                                    HRD / Kepegawaian
                                @elseif(Auth::user()->isKetua())
                                    Ketua STIKes
                                @else
                                    Administrator
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2.5 rounded-xl bg-rose-500/15 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 transition-all text-xs font-bold flex items-center gap-1.5" title="Keluar dari Sistem">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span class="md:hidden">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 z-10 overflow-x-hidden">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-950/80 border border-emerald-700/60 text-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xl flex items-start gap-3.5 backdrop-blur-md animate-in fade-in slide-in-from-top-2 duration-300">
                <div class="p-2 bg-emerald-500/20 text-emerald-400 rounded-xl shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div class="whitespace-pre-line text-xs sm:text-sm font-medium leading-relaxed pt-0.5">{!! session('success') !!}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-rose-950/80 border border-rose-700/60 text-rose-200 rounded-2xl p-4 sm:p-5 shadow-xl flex items-start gap-3.5 backdrop-blur-md animate-in fade-in slide-in-from-top-2 duration-300">
                <div class="p-2 bg-rose-500/20 text-rose-400 rounded-xl shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="text-xs sm:text-sm font-medium leading-relaxed pt-0.5">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 bg-blue-950/80 border border-blue-700/60 text-blue-200 rounded-2xl p-4 sm:p-5 shadow-xl flex items-start gap-3.5 backdrop-blur-md animate-in fade-in slide-in-from-top-2 duration-300">
                <div class="p-2 bg-blue-500/20 text-blue-400 rounded-xl shrink-0">
                    <i data-lucide="info" class="w-5 h-5"></i>
                </div>
                <div class="text-xs sm:text-sm font-medium leading-relaxed pt-0.5">{{ session('info') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Admin Footer -->
    <footer class="bg-slate-950/90 text-slate-400 py-6 border-t border-slate-800/80 mt-auto z-10 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-center sm:text-left">
            <p class="font-medium text-slate-400"><strong class="text-white font-display">REHAT-PW</strong> &bull; STIKes Panti Waluya Malang &copy; {{ date('Y') }}</p>
            <p class="text-slate-500 text-[11px]">Sistem Rekapitulasi &amp; Pengajuan Cuti Multi-Level Berjenjang</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        function toggleAppMobileMenu() {
            const menu = document.getElementById('appNavMenu');
            const openIcon = document.getElementById('appMenuOpenIcon');
            const closeIcon = document.getElementById('appMenuCloseIcon');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                openIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
                openIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
