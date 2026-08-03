@extends('layouts.app')

@section('title', 'Tambah Pegawai Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Data Pegawai Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Isi formulir data kepegawaian STIKes Panti Waluya Malang.</p>
        </div>
        <a href="{{ route('pegawai.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition-colors">
            &larr; Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('pegawai.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nip" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        NIP / NIK Pegawai <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nip" id="nip" 
                           value="{{ old('nip') }}" 
                           placeholder="Contoh: 202401001" 
                           class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-mono font-bold focus:ring-blue-500 focus:border-blue-500" required>
                    @error('nip') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama" id="nama" 
                           value="{{ old('nama') }}" 
                           placeholder="Contoh: Ns. Maria Fransiska, S.Kep., M.Kep." 
                           class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-blue-500 focus:border-blue-500" required>
                    @error('nama') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="divisi_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Divisi / Program Studi <span class="text-rose-500">*</span>
                    </label>
                    <select name="divisi_id" id="divisi_id" class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-medium focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled selected>-- Pilih Divisi / Prodi --</option>
                        @foreach($divisis as $div)
                            <option value="{{ $div->id }}" {{ old('divisi_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                    @error('divisi_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jabatan" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Jabatan Pegawai <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="jabatan" id="jabatan" 
                           value="{{ old('jabatan') }}" 
                           placeholder="Contoh: Dosen D3 Keperawatan / Staff Admin" 
                           class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-medium focus:ring-blue-500 focus:border-blue-500" required>
                    @error('jabatan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        Email Resmi <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" 
                           value="{{ old('email') }}" 
                           placeholder="pegawai@stikespantiwaluya.ac.id" 
                           class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-medium focus:ring-blue-500 focus:border-blue-500" required>
                    @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                        No. HP / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_hp" id="no_hp" 
                           value="{{ old('no_hp') }}" 
                           placeholder="081234567890" 
                           class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-medium focus:ring-blue-500 focus:border-blue-500" required>
                    @error('no_hp') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="jatah_cuti" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Jatah Kuota Cuti Tahunan (Hari) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="jatah_cuti" id="jatah_cuti" 
                       value="{{ old('jatah_cuti', 12) }}" 
                       min="0" max="30" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-blue-500 focus:border-blue-500" required>
                <span class="text-[11px] text-slate-500 mt-1 block">Default kuota standar STIKes: 12 hari kerja per tahun</span>
                @error('jatah_cuti') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('pegawai.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl text-xs shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Data Pegawai</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
