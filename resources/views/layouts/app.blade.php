<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Portal') - REHAT-PW STIKes Panti Waluya Malang</title>
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
            background-color: #F1F5F9;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-slate-800">

    <!-- Top Bar -->
    <header class="bg-slate-900 text-white shadow-md sticky top-0 z-50 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes Panti Waluya" class="h-10 w-auto object-contain drop-shadow">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-xl tracking-tight text-white">REHAT-PW</span>
                                <span class="text-[10px] uppercase font-bold text-amber-300 bg-amber-500/20 px-2 py-0.5 rounded border border-amber-500/30">Portal Cuti</span>
                            </div>
                            <span class="text-xs text-blue-300 font-medium">STIKes Panti Waluya Malang</span>
                        </div>
                    </a>
                </div>

                <!-- User Profile & Action -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-blue-300 flex items-center justify-end gap-1">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                            @if(Auth::user()->isKadiv())
                                Kepala Divisi {{ Auth::user()->divisi ? '(' . Auth::user()->divisi->nama_divisi . ')' : '' }}
                            @elseif(Auth::user()->isHrd())
                                Tim HRD & Kepegawaian (Admin)
                            @elseif(Auth::user()->isKetua())
                                Ketua STIKes
                            @else
                                {{ strtoupper(Auth::user()->role) }}
                            @endif
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-rose-600/90 hover:bg-rose-600 text-white text-xs font-semibold rounded-lg shadow transition-colors flex items-center gap-1.5">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Sub Navigation Menu -->
    <div class="bg-white border-b border-slate-200 shadow-sm sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-1 py-2 overflow-x-auto">
                <!-- Dashboard (Tahun Berjalan) -->
                <a href="{{ route('dashboard') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-blue-600"></i>
                    <span>Dashboard (Tahun Berjalan)</span>
                </a>

                <!-- Arsip Cuti Tahunan (Per Tahun) -->
                <a href="{{ route('arsip.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('arsip.*') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i data-lucide="archive" class="w-4 h-4 text-amber-600"></i>
                    <span>Arsip Cuti Tahunan</span>
                </a>

                {{-- FITUR KHUSUS AKUN HRD --}}
                @if(Auth::user()->isHrd())
                    <a href="{{ route('reports.index') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                        <span>Laporan &amp; Export Cuti</span>
                    </a>

                    <a href="{{ route('users.index') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="shield-check" class="w-4 h-4 text-purple-600"></i>
                        <span>Kelola User (Login)</span>
                    </a>

                    <a href="{{ route('pegawai.index') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('pegawai.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="users" class="w-4 h-4 text-indigo-600"></i>
                        <span>Kelola Pegawai (CRUD)</span>
                    </a>

                    <a href="{{ route('divisi.index') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('divisi.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="building-2" class="w-4 h-4 text-teal-600"></i>
                        <span>Kelola Divisi/Prodi (CRUD)</span>
                    </a>
                @endif

                <a href="{{ route('public.pengajuan') }}" target="_blank"
                   class="ml-auto px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 flex items-center gap-1 rounded hover:bg-slate-50">
                    <span>Lihat Form Publik</span>
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                </a>
            </nav>
        </div>
    </div>

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

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" class="h-5 w-auto" alt="Logo">
                <span class="font-bold text-slate-700">REHAT-PW</span>
                <span>&bull; STIKes Panti Waluya Malang</span>
            </div>
            <div>Sistem Informasi Cuti Pegawai &copy; {{ date('Y') }}</div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
