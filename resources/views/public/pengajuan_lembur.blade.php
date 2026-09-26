@extends('layouts.public')

@section('title', 'Form Pengajuan Klaim Jam Lembur')

@section('content')
<div class="max-w-4xl mx-auto w-full overflow-x-hidden">
    
    <!-- Hero Header Banner (Amber/Orange Theme) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-amber-950/80 to-slate-950 p-6 sm:p-10 text-white shadow-2xl border border-amber-800/40 mb-8">
        <!-- Ambient Background Mesh -->
        <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-amber-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-10 h-48 w-48 rounded-full bg-orange-500/15 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/15 text-amber-300 border border-amber-400/30 text-[11px] font-bold uppercase tracking-wider rounded-full mb-3 backdrop-blur-sm">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>Simpanan Jam Lembur Pegawai</span>
                </div>
                <h1 class="font-display text-2xl sm:text-4xl font-extrabold tracking-tight text-white mb-2.5 leading-tight">
                    Formulir Klaim Jam Lembur
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    STIKes Panti Waluya Malang. Pengajuan lembur minimal <strong class="text-amber-300">30 menit</strong>. Akumulasi <strong class="text-amber-300">9 Jam = 1 Hari Libur Kompensasi</strong> atau bisa diambil untuk <strong class="text-amber-300">Izin Pulang Cepat / Terlambat (Maks. 3 Jam)</strong>.
                </p>
            </div>

            <!-- Conversion Rule Card -->
            <div class="shrink-0 flex sm:flex-col gap-2.5 bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 backdrop-blur-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                        <i data-lucide="zap" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-[10px] text-amber-300 font-bold uppercase tracking-wider">Rumus Konversi</div>
                        <div class="text-xs font-black text-white font-display">9 Jam = 1 Hari Libur</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container Card -->
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 p-6 sm:p-10 w-full max-w-full text-slate-900">
        @if($pegawais->isEmpty())
            <div class="p-8 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-center">
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="alert-circle" class="w-7 h-7"></i>
                </div>
                <h3 class="font-display font-bold text-lg text-slate-900">Belum Ada Data Pegawai</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">Data pegawai belum diinput ke sistem oleh HRD.</p>
            </div>
        @else
            <form action="{{ route('public.pengajuan_lembur.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 max-w-full">
                @csrf

                <!-- Section 1: Identitas Pegawai -->
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-2xl bg-amber-600 text-white flex items-center justify-center font-display font-black text-sm shadow-md shadow-amber-500/30 shrink-0">
                            1
                        </div>
                        <div>
                            <h2 class="font-display text-base sm:text-lg font-bold text-slate-900 leading-tight">Identitas Pegawai Pemohon Lembur</h2>
                            <p class="text-slate-500 text-xs">Pilih identitas Anda untuk mencatatkan klaim simpanan lembur</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Pegawai Selection -->
                        <div class="md:col-span-2">
                            <label for="pegawai_id" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Pilih Pegawai <span class="text-rose-500">*</span>
                            </label>
                            <select name="pegawai_id" id="pegawai_id" class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 p-3 sm:p-3.5 bg-slate-50 hover:bg-white text-slate-900 border text-xs sm:text-sm font-semibold transition-all cursor-pointer" required onchange="updatePegawaiInfo(this)">
                                <option value="" disabled selected>-- Pilih Nama / NIP Pegawai --</option>
                                @foreach($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}" 
                                            data-nip="{{ $pegawai->nip }}"
                                            data-divisi="{{ $pegawai->divisi->nama_divisi ?? '-' }}"
                                            data-lembur="{{ $pegawai->saldo_lembur_formatted }}"
                                            {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }} ({{ $pegawai->nip }}) &bull; {{ $pegawai->divisi->nama_divisi ?? 'Tanpa Divisi' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pegawai_id') <p class="text-xs text-rose-600 font-semibold mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- Info Card Preview -->
                        <div id="pegawai-detail-card" class="hidden md:col-span-2 bg-gradient-to-br from-slate-900 via-amber-950/90 to-slate-900 text-white rounded-2xl p-5 shadow-xl border border-amber-900/60 animate-in fade-in zoom-in-95 duration-200">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-3 flex items-center gap-1.5">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-amber-400"></i>
                                <span>Data Pegawai Terverifikasi</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">NIP / NIK</span>
                                    <span id="preview-nip" class="font-mono font-bold text-white text-xs sm:text-sm block mt-0.5"></span>
                                </div>
                                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Divisi / Prodi</span>
                                    <span id="preview-divisi" class="font-bold text-white text-xs sm:text-sm block mt-0.5"></span>
                                </div>
                                <div class="bg-amber-500/15 rounded-xl p-3 border border-amber-500/30">
                                    <span class="text-amber-300 block text-[10px] uppercase font-bold tracking-wider">Saldo Simpanan Lembur</span>
                                    <span id="preview-lembur" class="font-bold text-amber-400 font-display text-base sm:text-lg block mt-0.5"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- Section 2: Details Lembur -->
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-2xl bg-orange-600 text-white flex items-center justify-center font-display font-black text-sm shadow-md shadow-orange-500/30 shrink-0">
                            2
                        </div>
                        <div>
                            <h2 class="font-display text-base sm:text-lg font-bold text-slate-900 leading-tight">Detail Waktu &amp; Kegiatan Lembur</h2>
                            <p class="text-slate-500 text-xs">Tentukan durasi jam, menit, serta deskripsi pekerjaan lembur</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Tanggal Lembur -->
                        <div class="md:col-span-2">
                            <label for="tanggal_lembur" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Tanggal Pelaksanaan Lembur <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_lembur" id="tanggal_lembur" 
                                   value="{{ old('tanggal_lembur', date('Y-m-d')) }}" 
                                   class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 p-3 sm:p-3.5 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-bold" required>
                            @error('tanggal_lembur') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Durasi Jam Lembur -->
                        <div>
                            <label for="durasi_jam" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Durasi Jam <span class="text-slate-400 font-normal">(Jam)</span>
                            </label>
                            <select name="durasi_jam" id="durasi_jam" class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 p-3 sm:p-3.5 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-bold">
                                @for($j = 0; $j <= 12; $j++)
                                    <option value="{{ $j }}" {{ old('durasi_jam', 1) == $j ? 'selected' : '' }}>{{ $j }} Jam</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Durasi Menit Lembur -->
                        <div>
                            <label for="durasi_menit" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Tambahan Menit <span class="text-slate-400 font-normal">(Menit)</span>
                            </label>
                            <select name="durasi_menit" id="durasi_menit" class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 p-3 sm:p-3.5 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-bold">
                                <option value="0" {{ old('durasi_menit') == 0 ? 'selected' : '' }}>0 Menit</option>
                                <option value="15" {{ old('durasi_menit') == 15 ? 'selected' : '' }}>15 Menit</option>
                                <option value="20" {{ old('durasi_menit') == 20 ? 'selected' : '' }}>20 Menit</option>
                                <option value="30" {{ old('durasi_menit') == 30 ? 'selected' : '' }}>30 Menit</option>
                                <option value="40" {{ old('durasi_menit') == 40 ? 'selected' : '' }}>40 Menit</option>
                                <option value="45" {{ old('durasi_menit') == 45 ? 'selected' : '' }}>45 Menit</option>
                                <option value="50" {{ old('durasi_menit') == 50 ? 'selected' : '' }}>50 Menit</option>
                            </select>
                            <span class="text-[11px] text-slate-500 mt-1 block">Minimal klaim lembur 30 menit</span>
                        </div>

                        <!-- Kegiatan / Alasan Lembur -->
                        <div class="md:col-span-2">
                            <label for="kegiatan" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Kegiatan / Uraian Tugas Lembur <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="kegiatan" id="kegiatan" rows="3" 
                                      placeholder="Contoh: Panitia Wisuda, Persiapan Akreditasi Program Studi, Dinas Jaga Pelayanan Hari Libur..." 
                                      class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 p-3.5 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-medium" required>{{ old('kegiatan') }}</textarea>
                            @error('kegiatan') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- File Bukti / Surat Tugas -->
                        <div class="md:col-span-2">
                            <label for="file_bukti" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Lampiran Surat Tugas / Foto Kegiatan <span class="text-slate-400 font-normal text-xs">(Opsional)</span>
                            </label>
                            <input type="file" name="file_bukti" id="file_bukti" 
                                   class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 p-2.5 bg-slate-50 border text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700">
                            <span class="text-[11px] text-slate-500 mt-1 block">Format: PDF, JPG, PNG (Maksimal 2MB)</span>
                            @error('file_bukti') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Button Toolbar -->
                <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 border-t border-slate-100">
                    <button type="reset" class="px-5 py-3.5 rounded-2xl border border-slate-300 text-slate-700 text-xs sm:text-sm font-bold hover:bg-slate-100 transition-colors text-center">
                        Reset Formulir
                    </button>
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:to-orange-500 text-white rounded-2xl font-display font-bold shadow-xl shadow-amber-600/30 hover:shadow-2xl transition-all duration-200 flex items-center justify-center gap-2.5 text-xs sm:text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Klaim Lembur Sekarang</span>
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
        const lembur = option.getAttribute('data-lembur') || '0 Menit';

        document.getElementById('preview-nip').innerText = nip;
        document.getElementById('preview-divisi').innerText = divisi;
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
