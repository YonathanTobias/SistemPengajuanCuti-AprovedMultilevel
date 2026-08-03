<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pengajuan Cuti') - STIKes Panti Waluya Malang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #F8FAFC;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-slate-800">

    <!-- Header Navigation -->
    <header class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white shadow-lg border-b border-indigo-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Branding -->
                <a href="{{ route('public.pengajuan') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 bg-white rounded-xl p-2 shadow-md flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-8 h-8 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-7a1 1 0 011-1h2a1 1 0 011 1v7m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold tracking-tight text-white group-hover:text-blue-200 transition-colors">STIKes Panti Waluya</div>
                        <div class="text-xs text-blue-200/80 font-medium">Sistem Pengajuan Cuti Pegawai (Malang)</div>
                    </div>
                </a>

                <!-- Nav Links -->
                <nav class="flex items-center gap-2 sm:gap-4">
                    <a href="{{ route('public.pengajuan') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('public.pengajuan') ? 'bg-white/15 text-white shadow-inner border border-white/20' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="file-plus-2" class="w-4 h-4"></i>
                        <span>Pengajuan Cuti</span>
                    </a>
                    <a href="{{ route('public.tracking') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('public.tracking') ? 'bg-white/15 text-white shadow-inner border border-white/20' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Lacak Status Cuti</span>
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="ml-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard ({{ strtoupper(Auth::user()->role) }})</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="ml-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span>Login Pejabat</span>
                        </a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
        <div class="max-w-7xl mx-auto px-4 text-center text-sm">
            <p class="font-medium text-slate-300">STIKes Panti Waluya Malang &copy; {{ date('Y') }}</p>
            <p class="text-xs text-slate-500 mt-1">Sistem Informasi Pengajuan Cuti Multi-Level Approval (Kaprodi/Kadiv &bull; HRD &bull; Ketua STIKes)</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
