@extends('layouts.app')

@section('title', 'Tambah Divisi / Prodi Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Divisi / Prodi Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi nama divisi dan sistem akan otomatis membuatkan akun login Kepala Divisi.</p>
        </div>
        <a href="{{ route('divisi.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition-colors">
            &larr; Kembali
        </a>
    </div>

    <!-- Special Feature Highlight Card -->
    <div class="bg-gradient-to-r from-teal-900 to-emerald-900 text-white rounded-2xl p-5 shadow-lg border border-teal-700/50">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center shrink-0 border border-white/20">
                <i data-lucide="user-check" class="w-5 h-5 text-teal-300"></i>
            </div>
            <div class="text-xs space-y-1">
                <span class="font-bold text-teal-200 uppercase tracking-wider text-[11px] block">Otomatisasi Akun Kadiv</span>
                <p class="text-slate-200">
                    Setelah Anda menyimpan divisi baru ini, sistem akan <strong>secara otomatis membuat akun user Kepala Divisi</strong> dengan role <code class="bg-teal-950 px-1 py-0.5 rounded text-teal-300 font-mono">kadiv</code> dan default password <code class="bg-teal-950 px-1 py-0.5 rounded text-teal-300 font-mono">password123</code>.
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('divisi.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="kode_divisi" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Kode Divisi / Prodi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_divisi" id="kode_divisi" 
                       value="{{ old('kode_divisi') }}" 
                       placeholder="Contoh: PRODI-D3KPR, PRODI-S1FAR, DIV-KEU" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-mono font-bold focus:ring-teal-500 focus:border-teal-500 uppercase" required>
                @error('kode_divisi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nama_divisi" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Nama Divisi / Program Studi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_divisi" id="nama_divisi" 
                       value="{{ old('nama_divisi') }}" 
                       placeholder="Contoh: Prodi D3 Keperawatan, Divisi Keuangan" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-teal-500 focus:border-teal-500" required>
                @error('nama_divisi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-bold uppercase text-slate-700 mb-1">
                    Deskripsi / Catatan Singkat
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="2" 
                          placeholder="Penjelasan singkat mengenai unit/divisi ini..." 
                          class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs focus:ring-teal-500 focus:border-teal-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="border-t border-slate-100 pt-5 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Opsional: Kustomisasi Akun Kadiv</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_kadiv" class="block text-xs font-semibold text-slate-700 mb-1">Nama Kepala Divisi</label>
                        <input type="text" name="nama_kadiv" id="nama_kadiv" 
                               value="{{ old('nama_kadiv') }}" 
                               placeholder="Biarkan kosong untuk nama otomatis" 
                               class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs">
                    </div>
                    <div>
                        <label for="email_kadiv" class="block text-xs font-semibold text-slate-700 mb-1">Custom Email Kadiv</label>
                        <input type="email" name="email_kadiv" id="email_kadiv" 
                               value="{{ old('email_kadiv') }}" 
                               placeholder="Biarkan kosong untuk email otomatis" 
                               class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('divisi.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold rounded-xl text-xs shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan &amp; Generate Akun Kadiv</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
