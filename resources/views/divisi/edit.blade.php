@extends('layouts.app')

@section('title', 'Edit Divisi / Prodi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Divisi / Prodi</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi divisi dan data Kepala Divisi.</p>
        </div>
        <a href="{{ route('divisi.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition-colors">
            &larr; Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('divisi.update', $divisi->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="kode_divisi" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Kode Divisi / Prodi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_divisi" id="kode_divisi" 
                       value="{{ old('kode_divisi', $divisi->kode_divisi) }}" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-mono font-bold focus:ring-teal-500 focus:border-teal-500 uppercase" required>
                @error('kode_divisi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nama_divisi" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Nama Divisi / Program Studi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_divisi" id="nama_divisi" 
                       value="{{ old('nama_divisi', $divisi->nama_divisi) }}" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-teal-500 focus:border-teal-500" required>
                @error('nama_divisi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Deskripsi / Catatan Singkat
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="2" 
                          class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs focus:ring-teal-500 focus:border-teal-500">{{ old('deskripsi', $divisi->deskripsi) }}</textarea>
            </div>

            @if($divisi->kadivUser)
                <div class="border-t border-slate-100 pt-5">
                    <label for="nama_kadiv" class="block text-xs font-bold uppercase text-slate-700 mb-1">Nama Kepala Divisi (Kadiv User)</label>
                    <input type="text" name="nama_kadiv" id="nama_kadiv" 
                           value="{{ old('nama_kadiv', $divisi->kadivUser->name) }}" 
                           class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-semibold">
                    <span class="text-[11px] text-slate-500 mt-1 block">Email Akun Kadiv: <code class="font-mono text-blue-700">{{ $divisi->kadivUser->email }}</code></span>
                </div>
            @endif

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('divisi.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Update Data Divisi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
