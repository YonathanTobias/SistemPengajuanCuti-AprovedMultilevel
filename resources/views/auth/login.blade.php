@extends('layouts.public')

@section('title', 'Login Portal REHAT-PW')

@section('content')
<div class="max-w-md mx-auto py-6">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <!-- Login Header -->
        <div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 p-8 text-white text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes Panti Waluya" class="h-16 w-auto mx-auto mb-3 object-contain drop-shadow">
            <h1 class="text-3xl font-black tracking-tight text-white">REHAT-PW</h1>
            <p class="text-xs text-amber-300 font-semibold uppercase tracking-wider mt-1">Portal Login Internal Pejabat &amp; HRD</p>
            <p class="text-[11px] text-blue-200/80 mt-1">STIKes Panti Waluya Malang</p>
        </div>

        <!-- Form -->
        <div class="p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Email Akun STIKes <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="email" name="email" id="email" 
                               value="{{ old('email') }}" 
                               placeholder="nama@stikespantiwaluya.ac.id" 
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 text-sm font-medium pl-10" required autofocus>
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                    </div>
                    @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" 
                               placeholder="••••••••" 
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 text-sm font-medium pl-10" required>
                        <i data-lucide="key-round" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                    </div>
                    @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-blue-900 focus:ring-blue-500">
                        <span class="text-slate-600">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-900 to-indigo-900 hover:from-blue-800 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 text-sm">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Dashboard REHAT-PW</span>
                </button>
            </form>

            <!-- Quick Demo Login Hint Card -->
            <div class="mt-8 pt-6 border-t border-slate-100 bg-slate-50 p-4 rounded-xl text-xs space-y-2">
                <span class="font-bold text-slate-700 block text-center mb-1">🔑 Demo Akun Kredensial (Default Password: <code class="text-blue-700 bg-blue-100 px-1 py-0.5 rounded">password123</code>)</span>
                <div class="space-y-1">
                    <button type="button" onclick="fillLogin('hrd@stikespantiwaluya.ac.id')" class="w-full text-left p-1.5 rounded hover:bg-white text-slate-600 hover:text-blue-900 flex justify-between font-mono">
                        <span>HRD: hrd@stikespantiwaluya.ac.id</span>
                        <span class="text-[10px] text-blue-600 font-sans font-bold">Isi Email</span>
                    </button>
                    <button type="button" onclick="fillLogin('ketua@stikespantiwaluya.ac.id')" class="w-full text-left p-1.5 rounded hover:bg-white text-slate-600 hover:text-blue-900 flex justify-between font-mono">
                        <span>Ketua: ketua@stikespantiwaluya.ac.id</span>
                        <span class="text-[10px] text-blue-600 font-sans font-bold">Isi Email</span>
                    </button>
                    <button type="button" onclick="fillLogin('kadiv.keperawatan@stikespantiwaluya.ac.id')" class="w-full text-left p-1.5 rounded hover:bg-white text-slate-600 hover:text-blue-900 flex justify-between font-mono">
                        <span>Kadiv: kadiv.keperawatan@stikespantiwaluya.ac.id</span>
                        <span class="text-[10px] text-blue-600 font-sans font-bold">Isi Email</span>
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
