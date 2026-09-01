<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Dashboard') - REHAT-PW STIKes Panti Waluya Malang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        html, body {
            font-family: 'Outfit', sans-serif;
            background-color: #F1F5F9;
            max-width: 100vw;
            overflow-x: hidden;
        }
        select {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-slate-800 antialiased max-w-full overflow-x-hidden">

    <!-- Top Navigation Bar -->
    <header class="bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 text-white shadow-md border-b border-indigo-800/60 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2.5 md:py-0 min-h-[4rem] gap-2 md:gap-0">
                <!-- Branding & Mobile Toggle -->
                <div class="flex items-center justify-between w-full md:w-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes Panti Waluya" class="h-9 sm:h-11 w-auto object-contain drop-shadow group-hover:scale-105 transition-transform shrink-0">
                        <div class="leading-tight">
                            <div class="flex items-center gap-1.5">
                                <span class="text-lg sm:text-2xl font-black tracking-tight text-white group-hover:text-blue-200 transition-colors">REHAT-PW</span>
                                <span class="text-[9px] uppercase font-bold text-teal-300 bg-teal-500/20 px-1.5 py-0.5 rounded border border-teal-500/30 hidden xs:inline-block">Portal</span>
                            </div>
                            <div class="text-[10px] sm:text-xs text-blue-200/80 font-medium truncate max-w-[200px] sm:max-w-none">STIKes Panti Waluya Malang</div>
                        </div>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="toggleAppMobileMenu()" class="md:hidden p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white focus:outline-none shrink-0" aria-label="Toggle Navigation">
                        <i data-lucide="menu" id="appMenuOpenIcon" class="w-5 h-5"></i>
                        <i data-lucide="x" id="appMenuCloseIcon" class="w-5 h-5 hidden"></i>
                    </button>
                </div>

                <!-- Nav Links & User Info -->
                <div id="appNavMenu" class="hidden md:flex flex-col md:flex-row items-stretch md:items-center gap-2 sm:gap-4 pt-2 md:pt-0 border-t md:border-t-0 border-white/10 text-xs sm:text-sm">
                    <nav class="flex flex-col md:flex-row items-stretch md:items-center gap-1 sm:gap-1.5">
                        <a href="{{ route('dashboard') }}" 
                           class="px-3 py-2 rounded-xl font-semibold transition-all flex items-center justify-center md:justify-start gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </a>

                        @if($isLemburEnabled ?? false)
                            <a href="{{ route('lembur.index') }}" 
                               class="px-3 py-2 rounded-xl font-semibold transition-all flex items-center justify-center md:justify-start gap-1.5 {{ request()->routeIs('lembur.*') ? 'bg-amber-500/30 text-amber-200 shadow-inner border border-amber-400/40 font-bold' : 'text-amber-200 hover:bg-white/10 hover:text-white' }}">
                                <i data-lucide="clock" class="w-4 h-4 text-amber-300"></i>
                                <span>Klaim Lembur</span>
                            </a>
                        @endif

                        <a href="{{ route('arsip.index') }}" 
                           class="px-3 py-2 rounded-xl font-semibold transition-all flex items-center justify-center md:justify-start gap-1.5 {{ request()->routeIs('arsip.*') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="archive" class="w-4 h-4"></i>
                            <span>Arsip Tahunan</span>
                        </a>

                        <a href="{{ route('reports.index') }}" 
                           class="px-3 py-2 rounded-xl font-semibold transition-all flex items-center justify-center md:justify-start gap-1.5 {{ request()->routeIs('reports.*') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            <span>Laporan &amp; Rekap</span>
                        </a>

                        @if(Auth::user()->isHrd())
                            <a href="{{ route('pegawai.index') }}" 
                               class="px-3 py-2 rounded-xl font-semibold transition-all flex items-center justify-center md:justify-start gap-1.5 {{ request()->routeIs('pegawai.*') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                                <i data-lucide="users" class="w-4 h-4"></i>
                                <span>Pegawai</span>
                            </a>
                            <a href="{{ route('divisi.index') }}" 
                               class="px-3 py-2 rounded-xl font-semibold transition-all flex items-center justify-center md:justify-start gap-1.5 {{ request()->routeIs('divisi.*') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                <span>Divisi/Prodi</span>
                            </a>
                            <a href="{{ route('users.index') }}" 
                               class="px-3 py-2 rounded-xl font-semibold transition-all flex items-center justify-center md:justify-start gap-1.5 {{ request()->routeIs('users.*') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                                <i data-lucide="user-cog" class="w-4 h-4"></i>
                                <span>User Akun</span>
                            </a>
                        @endif
                    </nav>

                    <div class="flex items-center justify-between md:justify-start gap-3 border-t md:border-t-0 md:border-l border-white/15 pt-2 md:pt-0 md:pl-4">
                        <div class="text-left leading-tight">
                            <div class="font-bold text-white text-xs">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-teal-300 font-semibold uppercase">
                                @if(Auth::user()->isKadiv())
                                    Kepala Divisi {{ Auth::user()->divisi->nama_divisi ?? '' }}
                                @elseif(Auth::user()->isHrd())
                                    HRD &amp; Kepegawaian
                                @elseif(Auth::user()->isKetua())
                                    Ketua STIKes
                                @else
                                    Administrator
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 rounded-xl bg-rose-500/20 hover:bg-rose-600 text-rose-200 hover:text-white transition-all text-xs font-semibold flex items-center gap-1" title="Keluar">
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
    <main class="flex-grow max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8 overflow-x-hidden">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 shadow-sm flex items-start gap-3">
                <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-600 shrink-0 mt-0.5"></i>
                <div class="whitespace-pre-line text-sm font-medium">{!! session('success') !!}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 shadow-sm flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-rose-600 shrink-0 mt-0.5"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl p-4 shadow-sm flex items-start gap-3">
                <i data-lucide="info" class="w-6 h-6 text-blue-600 shrink-0 mt-0.5"></i>
                <div class="text-sm font-medium">{{ session('info') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500 text-center sm:text-left">
            <p><strong class="text-slate-800">REHAT-PW</strong> &bull; STIKes Panti Waluya Malang &copy; {{ date('Y') }}</p>
            <p>Sistem Rekapitulasi &amp; Pengajuan Cuti Berjenjang</p>
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
