@extends('layouts.app')

@section('title', 'Tambah Akun User Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Akun User Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Buat akun pejabat (Kadiv, HRD, atau Ketua STIKes) untuk akses login sistem</p>
        </div>
        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs flex items-center gap-1.5">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama / Jabatan Generik -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Nama Pengguna / Judul Akun <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                       placeholder="Contoh: Kepala Prodi S1 Keperawatan / Tim HRD" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-medium focus:ring-blue-500" required>
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Email Login -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Email Login <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       placeholder="Contoh: kadiv.keperawatan@stikespantiwaluya.ac.id" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-mono focus:ring-blue-500" required>
                @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Password Login <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password" id="password" value="password123" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-medium focus:ring-blue-500" required>
                <span class="text-[11px] text-slate-500 mt-1 block">Default: <code class="bg-slate-200 px-1 rounded">password123</code></span>
                @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Role Akses -->
            <div>
                <label for="role" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Role Akses Sistem <span class="text-rose-500">*</span>
                </label>
                <select name="role" id="role" onchange="toggleDivisiSelection(this.value)" 
                        class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-blue-500" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="kadiv" {{ old('role') == 'kadiv' ? 'selected' : '' }}>Kepala Divisi / Kaprodi (Level 1 Approval)</option>
                    <option value="hrd" {{ old('role') == 'hrd' ? 'selected' : '' }}>Tim HRD &amp; Kepegawaian (Level 2 Approval &amp; Full Admin)</option>
                    <option value="ketua" {{ old('role') == 'ketua' ? 'selected' : '' }}>Ketua STIKes (Level 3 Final Approval)</option>
                </select>
                @error('role') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Divisi / Prodi (Khusus Kadiv) -->
            <div id="divisiContainer" class="hidden">
                <label for="divisi_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Pilih Divisi / Prodi Terkait <span class="text-rose-500">*</span>
                </label>
                <select name="divisi_id" id="divisi_id" class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-medium focus:ring-blue-500">
                    <option value="" disabled selected>-- Pilih Divisi / Prodi --</option>
                    @foreach($divisis as $div)
                        <option value="{{ $div->id }}" {{ old('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->nama_divisi }}
                        </option>
                    @endforeach
                </select>
                @error('divisi_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white font-bold rounded-xl text-xs shadow-md">
                    Simpan Akun User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleDivisiSelection(role) {
        const container = document.getElementById('divisiContainer');
        if (role === 'kadiv') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const roleSelect = document.getElementById('role');
        if (roleSelect && roleSelect.value) {
            toggleDivisiSelection(roleSelect.value);
        }
    });
</script>
@endpush
