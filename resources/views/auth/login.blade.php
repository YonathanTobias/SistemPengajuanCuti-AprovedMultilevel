@extends('layouts.public')

@section('title', 'Login Portal REHAT-PW')

@section('content')
<div class="max-w-md mx-auto py-4 sm:py-8">
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden text-slate-900">
        
        <!-- Login Header Banner -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950 p-8 text-white text-center border-b border-slate-800">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="relative inline-block mb-3">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl blur opacity-75"></div>
                    <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes Panti Waluya" class="relative h-16 w-auto mx-auto object-contain drop-shadow">
                </div>
                <h1 class="font-display text-2xl sm:text-3xl font-black tracking-tight text-white">REHAT-PW</h1>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-500/15 text-teal-300 border border-teal-500/30 rounded-full text-[10px] font-extrabold uppercase tracking-wider mt-2">
                    <i data-lucide="lock" class="w-3 h-3 text-teal-400"></i>
                    <span>Portal Autentikasi Pejabat</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">STIKes Panti Waluya Malang</p>
            </div>
        </div>

        <!-- Form Area -->
        <div class="p-6 sm:p-8 space-y-6">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Email Resmi Akun <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email') }}" 
                               placeholder="nama@stikespantiwaluya.ac.id" 
                               class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 p-3.5 bg-slate-50 text-slate-900 text-xs sm:text-sm font-semibold pl-10 border transition-all" required autofocus>
                    </div>
                    @error('email') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="key-round" class="w-4 h-4"></i>
                        </div>
                        <input type="password" name="password" id="password" 
                               placeholder="••••••••" 
                               class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 p-3.5 bg-slate-50 text-slate-900 text-xs sm:text-sm font-semibold pl-10 border transition-all" required>
                    </div>
                    @error('password') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 font-medium">
                        <input type="checkbox" name="remember" class="rounded-lg text-blue-600 focus:ring-blue-500 w-4 h-4 border-slate-300">
                        <span>Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white font-display font-bold rounded-2xl shadow-xl shadow-blue-600/30 hover:shadow-2xl transition-all flex items-center justify-center gap-2 text-xs sm:text-sm">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Dashboard Sistem</span>
                </button>
            </form>

            <!-- Quick Demo Login Hint Card -->
            <div class="pt-5 border-t border-slate-100 bg-slate-50/80 rounded-2xl p-4 text-xs space-y-2.5 border">
                <div class="flex items-center justify-between text-slate-700 font-bold">
                    <span class="flex items-center gap-1.5 text-[11px] uppercase tracking-wider text-slate-500">
                        <i data-lucide="key" class="w-3.5 h-3.5 text-blue-600"></i>
                        <span>Akses Demo Cepat</span>
                    </span>
                    <span class="text-[10px] font-mono text-blue-700 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200">Pass: password123</span>
                </div>
                <div class="space-y-1.5">
                    <button type="button" onclick="fillLogin('hrd@stikespantiwaluya.ac.id')" class="w-full text-left p-2 rounded-xl hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 text-slate-700 flex items-center justify-between transition-all group">
                        <span class="font-mono text-[11px] group-hover:text-blue-600">HRD: hrd@stikespantiwaluya.ac.id</span>
                        <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">Pilih</span>
                    </button>
                    <button type="button" onclick="fillLogin('ketua@stikespantiwaluya.ac.id')" class="w-full text-left p-2 rounded-xl hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 text-slate-700 flex items-center justify-between transition-all group">
                        <span class="font-mono text-[11px] group-hover:text-blue-600">Ketua: ketua@stikespantiwaluya.ac.id</span>
                        <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">Pilih</span>
                    </button>
                    <button type="button" onclick="fillLogin('kadiv.keperawatan@stikespantiwaluya.ac.id')" class="w-full text-left p-2 rounded-xl hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 text-slate-700 flex items-center justify-between transition-all group">
                        <span class="font-mono text-[11px] group-hover:text-blue-600">Kadiv: kadiv.keperawatan@stikespantiwaluya.ac.id</span>
                        <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">Pilih</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillLogin(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password123';
    }
</script>
@endpush
