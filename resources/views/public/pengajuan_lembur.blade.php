@extends('layouts.public')

@section('title', 'Form Pengajuan Klaim Jam Lembur')

@section('content')
<div class="max-w-4xl mx-auto w-full overflow-x-hidden">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-amber-900 via-yellow-950 to-slate-900 text-white rounded-2xl p-5 sm:p-8 shadow-xl mb-6 sm:mb-8 border border-amber-700/40 relative overflow-hidden">
        <div class="relative z-10">
            <span class="px-2.5 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] sm:text-xs font-semibold uppercase tracking-wider rounded-full inline-block mb-2">
                Simpanan Jam Lembur (TOIL)
            </span>
            <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-white mb-2 leading-tight">Formulir Klaim Jam Lembur Pegawai</h1>
            <p class="text-amber-200 text-xs sm:text-sm max-w-2xl leading-relaxed">
                STIKes Panti Waluya Malang. Pengajuan lembur minimal <strong>30 menit</strong> (misal: 45 menit, 1 jam 20 menit). Akumulasi <strong>9 Jam = 1 Hari Libur Kompensasi</strong> atau bisa digunakan untuk <strong>Izin Pulang Cepat / Terlambat (Maks. 3 Jam)</strong>.
            </p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-4 sm:p-8 w-full max-w-full overflow-hidden">
        @if($pegawais->isEmpty())
            <div class="p-6 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-center">
                <i data-lucide="alert-circle" class="w-10 h-10 mx-auto text-amber-600 mb-2"></i>
                <p class="font-semibold text-lg">Belum Ada Data Pegawai</p>
                <p class="text-sm text-amber-700 mt-1">Data pegawai belum diinput ke sistem oleh HRD.</p>
            </div>
        @else
            <form action="{{ route('public.pengajuan_lembur.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-full">
                @csrf

                <!-- Section 1: Data Pegawai -->
                <div class="border-b border-slate-200 pb-6">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-amber-100 text-amber-800 rounded-full flex items-center justify-center text-xs font-extrabold shrink-0">1</span>
                        Identitas Pegawai Pemohon Lembur
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Pegawai Selection -->
                        <div class="md:col-span-2 w-full max-w-full overflow-hidden">
                            <label for="pegawai_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Pilih Pegawai <span class="text-rose-500">*</span>
                            </label>
                            <select name="pegawai_id" id="pegawai_id" class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-2.5 sm:p-3 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-medium truncate" required onchange="updatePegawaiInfo(this)">
                                <option value="" disabled selected>-- Pilih Nama / NIP Pegawai --</option>
                                @foreach($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}" 
                                            data-nip="{{ $pegawai->nip }}"
                                            data-divisi="{{ $pegawai->divisi->nama_divisi ?? '-' }}"
                                            data-jabatan="{{ $pegawai->jabatan }}"
                                            data-lembur="{{ $pegawai->saldo_lembur_formatted }}"
                                            {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }} ({{ $pegawai->nip }}) - {{ $pegawai->divisi->nama_divisi ?? 'Tanpa Divisi' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pegawai_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Info Card Preview -->
                        <div id="pegawai-detail-card" class="hidden md:col-span-2 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <span class="text-slate-500 block font-medium text-[11px]">NIP</span>
                                    <span id="preview-nip" class="font-bold text-slate-900 text-xs sm:text-sm font-mono"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-medium text-[11px]">Divisi / Prodi</span>
                                    <span id="preview-divisi" class="font-bold text-slate-900 text-xs sm:text-sm"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-medium text-[11px]">Jabatan</span>
                                    <span id="preview-jabatan" class="font-bold text-slate-900 text-xs sm:text-sm"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-medium text-[11px]">Saldo Simpanan Lembur</span>
                                    <span id="preview-lembur" class="font-bold text-amber-800 text-sm sm:text-base"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Details Lembur -->
                <div class="border-b border-slate-200 pb-6">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-amber-100 text-amber-800 rounded-full flex items-center justify-center text-xs font-extrabold shrink-0">2</span>
                        Detail Waktu &amp; Kegiatan Lembur
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Tanggal Lembur -->
                        <div class="md:col-span-2">
                            <label for="tanggal_lembur" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Tanggal Pelaksanaan Lembur <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_lembur" id="tanggal_lembur" 
                                   value="{{ old('tanggal_lembur', date('Y-m-d')) }}" 
                                   class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-2.5 sm:p-3 bg-slate-50 border text-slate-900 text-xs sm:text-sm font-bold" required>
                            @error('tanggal_lembur') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Durasi Jam Lembur -->
                        <div>
                            <label for="durasi_jam" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Durasi Jam <span class="text-slate-500 font-normal">(Jam)</span>
                            </label>
                            <select name="durasi_jam" id="durasi_jam" class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-2.5 sm:p-3 bg-slate-50 border text-slate-900 text-xs sm:text-sm font-bold">
                                @for($j = 0; $j <= 12; $j++)
                                    <option value="{{ $j }}" {{ old('durasi_jam', 1) == $j ? 'selected' : '' }}>{{ $j }} Jam</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Durasi Menit Lembur -->
                        <div>
                            <label for="durasi_menit" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Tambahan Menit <span class="text-slate-500 font-normal">(Menit)</span>
                            </label>
                            <select name="durasi_menit" id="durasi_menit" class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-2.5 sm:p-3 bg-slate-50 border text-slate-900 text-xs sm:text-sm font-bold">
                                <option value="0" {{ old('durasi_menit') == 0 ? 'selected' : '' }}>0 Menit</option>
                                <option value="15" {{ old('durasi_menit') == 15 ? 'selected' : '' }}>15 Menit</option>
                                <option value="20" {{ old('durasi_menit') == 20 ? 'selected' : '' }}>20 Menit</option>
                                <option value="30" {{ old('durasi_menit') == 30 ? 'selected' : '' }}>30 Menit</option>
                                <option value="40" {{ old('durasi_menit') == 40 ? 'selected' : '' }}>40 Menit</option>
                                <option value="45" {{ old('durasi_menit') == 45 ? 'selected' : '' }}>45 Menit</option>
                                <option value="50" {{ old('durasi_menit') == 50 ? 'selected' : '' }}>50 Menit</option>
                            </select>
                            <span class="text-[10px] sm:text-[11px] text-slate-500 mt-1 block">Minimal klaim lembur 30 menit</span>
                        </div>

                        <!-- Kegiatan / Alasan Lembur -->
                        <div class="md:col-span-2">
                            <label for="kegiatan" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Kegiatan / Keperluan Lembur <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="kegiatan" id="kegiatan" rows="3" 
                                      placeholder="Contoh: Panitia Wisuda, Persiapan Akreditasi Prodi, Dinas Jaga Hari Libur..." 
                                      class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-3 bg-slate-50 border text-slate-900 text-xs sm:text-sm font-medium" required>{{ old('kegiatan') }}</textarea>
                            @error('kegiatan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- File Bukti / Surat Tugas -->
                        <div class="md:col-span-2 w-full max-w-full overflow-hidden">
                            <label for="file_bukti" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Lampiran Surat Tugas / Foto Kegiatan <span class="text-slate-400 font-normal text-xs">(Opsional)</span>
                            </label>
                            <input type="file" name="file_bukti" id="file_bukti" 
                                   class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-2 bg-slate-50 border text-xs text-slate-600">
                            <span class="text-[10px] sm:text-[11px] text-slate-500">Format: PDF, JPG, PNG (Maks 2MB)</span>
                            @error('file_bukti') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <button type="reset" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors text-center">
                        Reset Form
                    </button>
                    <button type="submit" class="px-6 py-3.5 bg-gradient-to-r from-amber-700 to-orange-800 hover:from-amber-600 hover:to-orange-700 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 text-xs sm:text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Klaim Lembur</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updatePegawaiInfo(select) {
        const option = select.options[select.selectedIndex];
        if (!option.value) return;

        const nip = option.getAttribute('data-nip');
        const divisi = option.getAttribute('data-divisi');
        const jabatan = option.getAttribute('data-jabatan');
        const lembur = option.getAttribute('data-lembur') || '0 Menit';

        document.getElementById('preview-nip').innerText = nip;
        document.getElementById('preview-divisi').innerText = divisi;
        document.getElementById('preview-jabatan').innerText = jabatan;
        document.getElementById('preview-lembur').innerText = lembur;

        document.getElementById('pegawai-detail-card').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('pegawai_id');
        if (select && select.value) {
            updatePegawaiInfo(select);
        }
    });
</script>
@endpush
