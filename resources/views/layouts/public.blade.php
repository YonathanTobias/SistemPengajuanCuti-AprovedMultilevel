<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Sistem Pengajuan Cuti') - REHAT-PW STIKes Panti Waluya Malang</title>
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
            background-color: #F8FAFC;
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

    <!-- Header Navigation -->
    <header class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white shadow-lg border-b border-indigo-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2.5 md:py-0 min-h-[4rem] gap-2 md:gap-0">
                <!-- Branding & Mobile Toggle -->
                <div class="flex items-center justify-between w-full md:w-auto">
                    <a href="{{ route('public.pengajuan') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes Panti Waluya" class="h-9 sm:h-11 w-auto object-contain drop-shadow group-hover:scale-105 transition-transform shrink-0">
                        <div class="leading-tight">
                            <div class="flex items-center gap-1.5">
                                <span class="text-lg sm:text-2xl font-black tracking-tight text-white group-hover:text-blue-200 transition-colors">REHAT-PW</span>
                                <span class="text-[9px] uppercase font-bold text-amber-300 bg-amber-500/20 px-1.5 py-0.5 rounded border border-amber-500/30 hidden xs:inline-block">Public</span>
                            </div>
                            <div class="text-[10px] sm:text-xs text-blue-200/80 font-medium truncate max-w-[200px] sm:max-w-none">STIKes Panti Waluya Malang</div>
                        </div>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white focus:outline-none shrink-0" aria-label="Toggle Navigation">
                        <i data-lucide="menu" id="menuOpenIcon" class="w-5 h-5"></i>
                        <i data-lucide="x" id="menuCloseIcon" class="w-5 h-5 hidden"></i>
                    </button>
                </div>

                <!-- Nav Links (Desktop & Mobile Dropdown) -->
                <nav id="navMenu" class="hidden md:flex flex-col md:flex-row items-stretch md:items-center gap-2 sm:gap-3 pt-2 md:pt-0 border-t md:border-t-0 border-white/10">
                    <a href="{{ route('public.pengajuan') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('public.pengajuan') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="file-plus-2" class="w-4 h-4"></i>
                        <span>Pengajuan Cuti</span>
                    </a>

                    <a href="{{ route('public.tracking') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all flex items-center justify-center md:justify-start gap-2 {{ request()->routeIs('public.tracking') ? 'bg-white/20 text-white shadow-inner border border-white/30' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Lacak Status Cuti</span>
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all flex items-center justify-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard ({{ strtoupper(Auth::user()->role) }})</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all flex items-center justify-center gap-2">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span>Login Pejabat</span>
                        </a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
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
    <footer class="bg-slate-900 text-slate-400 py-6 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-center sm:text-left">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" class="h-5 w-auto" alt="Logo">
                <p class="font-medium text-slate-300"><strong class="text-white">REHAT-PW</strong> &bull; STIKes Panti Waluya Malang &copy; {{ date('Y') }}</p>
            </div>
            <p class="text-xs text-slate-500">Sistem Informasi Pengajuan Cuti Multi-Level Approval</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        function toggleMobileMenu() {
            const menu = document.getElementById('navMenu');
            const openIcon = document.getElementById('menuOpenIcon');
            const closeIcon = document.getElementById('menuCloseIcon');

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
