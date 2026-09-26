@extends('layouts.public')

@section('title', 'Form Pengajuan Cuti Pegawai')

@section('content')
<div class="max-w-4xl mx-auto w-full overflow-x-hidden">
    
    <!-- Hero Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 p-6 sm:p-10 text-white shadow-2xl border border-slate-800/80 mb-8">
        <!-- Ambient Background Mesh -->
        <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-10 h-48 w-48 rounded-full bg-teal-500/15 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/15 text-blue-300 border border-blue-400/30 text-[11px] font-bold uppercase tracking-wider rounded-full mb-3 backdrop-blur-sm">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-blue-400"></i>
                    <span>Layanan Resmi Pegawai (1 Hari Cuti)</span>
                </div>
                <h1 class="font-display text-2xl sm:text-4xl font-extrabold tracking-tight text-white mb-2.5 leading-tight">
                    Formulir Pengajuan Cuti
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    STIKes Panti Waluya Malang. Pengajuan cuti harian berjenjang dengan verifikasi langsung oleh 
                    <strong class="text-white">Kepala Divisi/Kaprodi &rarr; HRD &rarr; Ketua STIKes</strong>.
                </p>
            </div>

            <!-- Quick Info Stat Pill -->
            <div class="shrink-0 flex sm:flex-col gap-2.5 bg-white/5 border border-white/10 rounded-2xl p-3.5 sm:p-4 backdrop-blur-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center shrink-0">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Multi-Level</div>
                        <div class="text-xs font-bold text-white">Otomatis &amp; Realtime</div>
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
                <p class="text-xs sm:text-sm text-slate-600 mt-1">Data pegawai belum diinput ke dalam sistem oleh HRD.</p>
            </div>
        @else
            <form action="{{ route('public.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 max-w-full">
                @csrf

                <!-- Section 1: Identitas Pegawai -->
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-display font-black text-sm shadow-md shadow-blue-500/30 shrink-0">
                            1
                        </div>
                        <div>
                            <h2 class="font-display text-base sm:text-lg font-bold text-slate-900 leading-tight">Identitas Pegawai Pemohon</h2>
                            <p class="text-slate-500 text-xs">Pilih nama Anda dari daftar database kepegawaian resmi STIKes</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Pegawai Selection -->
                        <div class="md:col-span-2">
                            <label for="pegawai_id" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Pilih Pegawai / Pengaju Cuti <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="pegawai_id" id="pegawai_id" class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 p-3 sm:p-3.5 bg-slate-50 hover:bg-white text-slate-900 border text-xs sm:text-sm font-semibold transition-all cursor-pointer" required onchange="updatePegawaiInfo(this)">
                                    <option value="" disabled selected>-- Pilih Nama / NIP Pegawai --</option>
                                    @foreach($pegawais as $pegawai)
                                        <option value="{{ $pegawai->id }}" 
                                                data-nip="{{ $pegawai->nip }}"
                                                data-divisi="{{ $pegawai->divisi->nama_divisi ?? '-' }}"
                                                data-sisa="{{ $pegawai->sisa_cuti }}"
                                                data-lembur="{{ $pegawai->saldo_lembur_formatted }}"
                                                {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                            {{ $pegawai->nama }} ({{ $pegawai->nip }}) &bull; {{ $pegawai->divisi->nama_divisi ?? 'Tanpa Divisi' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('pegawai_id') <p class="text-xs text-rose-600 font-semibold mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- Info Card Preview (Appears when employee selected) -->
                        <div id="pegawai-detail-card" class="hidden md:col-span-2 bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white rounded-2xl p-5 shadow-xl border border-slate-800 animate-in fade-in zoom-in-95 duration-200">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-blue-300 mb-3 flex items-center gap-1.5">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-teal-400"></i>
                                <span>Data Pegawai Terverifikasi</span>
                            </div>
                            <div class="grid grid-cols-2 {{ ($isLemburEnabled ?? false) ? 'sm:grid-cols-4' : 'sm:grid-cols-3' }} gap-4 text-xs">
                                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">NIP / NIK</span>
                                    <span id="preview-nip" class="font-mono font-bold text-white text-xs sm:text-sm block mt-0.5"></span>
                                </div>
                                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Divisi / Prodi</span>
                                    <span id="preview-divisi" class="font-bold text-white text-xs sm:text-sm block mt-0.5"></span>
                                </div>
                                <div class="bg-emerald-500/10 rounded-xl p-3 border border-emerald-500/30">
                                    <span class="text-emerald-300 block text-[10px] uppercase font-bold tracking-wider">Sisa Cuti Tahunan</span>
                                    <span id="preview-sisa" class="font-bold text-emerald-400 font-display text-base sm:text-lg block mt-0.5"></span>
                                </div>
                                @if($isLemburEnabled ?? false)
                                    <div class="bg-amber-500/10 rounded-xl p-3 border border-amber-500/30">
                                        <span class="text-amber-300 block text-[10px] uppercase font-bold tracking-wider">Saldo Jam Lembur</span>
                                        <span id="preview-lembur" class="font-bold text-amber-400 font-display text-base sm:text-lg block mt-0.5"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- Section 2: Detail Pengajuan Cuti -->
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-display font-black text-sm shadow-md shadow-indigo-500/30 shrink-0">
                            2
                        </div>
                        <div>
                            <h2 class="font-display text-base sm:text-lg font-bold text-slate-900 leading-tight">Detail Tanggal &amp; Jenis Cuti</h2>
                            <p class="text-slate-500 text-xs">Pilih jenis cuti dan tanggal pelaksanaan (1 hari kerja)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Tanggal Pelaksanaan -->
                        <div>
                            <label for="tanggal_cuti" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_cuti" id="tanggal_cuti" 
                                   value="{{ old('tanggal_cuti', date('Y-m-d')) }}" 
                                   class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 p-3 sm:p-3.5 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-bold" required>
                            <span class="text-[11px] text-slate-500 mt-1 block">Tentukan 1 hari kerja untuk pengajuan ini</span>
                            @error('tanggal_cuti') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jenis Cuti -->
                        <div>
                            <label for="jenis_cuti" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Jenis Cuti <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_cuti" id="jenis_cuti" onchange="toggleIzinJam(this.value)" class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 p-3 sm:p-3.5 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-semibold" required>
                                <option value="Cuti Tahunan" {{ old('jenis_cuti') == 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan (Potong 1 Hari Kuota)</option>
                                
                                @if($isLemburEnabled ?? false)
                                    <option value="Cuti Kompensasi Lembur" {{ old('jenis_cuti') == 'Cuti Kompensasi Lembur' ? 'selected' : '' }}>Cuti Kompensasi Lembur (Tukar 9 Jam Lembur = 1 Hari Libur)</option>
                                    <option value="Izin Pulang Cepat" {{ old('jenis_cuti') == 'Izin Pulang Cepat' ? 'selected' : '' }}>Izin Pulang Cepat (Potong Saldo Lembur, Maks. 3 Jam)</option>
                                    <option value="Izin Datang Terlambat" {{ old('jenis_cuti') == 'Izin Datang Terlambat' ? 'selected' : '' }}>Izin Datang Terlambat (Potong Saldo Lembur, Maks. 3 Jam)</option>
                                @endif

                                <option value="Cuti Sakit" {{ old('jenis_cuti') == 'Cuti Sakit' ? 'selected' : '' }}>Cuti Sakit (1 Hari)</option>
                                <option value="Cuti Melahirkan" {{ old('jenis_cuti') == 'Cuti Melahirkan' ? 'selected' : '' }}>Cuti Melahirkan (1 Hari)</option>
                                <option value="Cuti Alasan Penting" {{ old('jenis_cuti') == 'Cuti Alasan Penting' ? 'selected' : '' }}>Cuti Alasan Penting (1 Hari)</option>
                                <option value="Cuti Besar" {{ old('jenis_cuti') == 'Cuti Besar' ? 'selected' : '' }}>Cuti Besar (1 Hari)</option>
                            </select>
                        </div>

                        @if($isLemburEnabled ?? false)
                            <!-- Opsi Durasi Jam & Menit (Muncul jika Pulang Cepat / Datang Terlambat) -->
                            <div id="durasiJamContainer" class="hidden md:col-span-2 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-300/80 rounded-2xl p-5 space-y-3">
                                <div class="flex items-center gap-2 text-xs font-bold text-amber-900 uppercase tracking-wide">
                                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                                    <span>Tentukan Durasi Izin (Maksimal 3 Jam) <span class="text-rose-500">*</span></span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="izin_jam" class="block text-[11px] font-bold text-slate-700 mb-1">Jumlah Jam</label>
                                        <select name="izin_jam" id="izin_jam" class="w-full rounded-xl border-amber-300 p-3 bg-white text-slate-900 text-xs sm:text-sm font-bold focus:ring-amber-500">
                                            <option value="0">0 Jam</option>
                                            <option value="1" selected>1 Jam</option>
                                            <option value="2">2 Jam</option>
                                            <option value="3">3 Jam (Maksimal)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="izin_menit" class="block text-[11px] font-bold text-slate-700 mb-1">Tambahan Menit</label>
                                        <select name="izin_menit" id="izin_menit" class="w-full rounded-xl border-amber-300 p-3 bg-white text-slate-900 text-xs sm:text-sm font-bold focus:ring-amber-500">
                                            <option value="0" selected>0 Menit</option>
                                            <option value="15">15 Menit</option>
                                            <option value="20">20 Menit</option>
                                            <option value="30">30 Menit (Setengah Jam)</option>
                                            <option value="45">45 Menit</option>
                                        </select>
                                    </div>
                                </div>
                                <span class="text-[11px] text-amber-800 font-medium block">Saldo jam lembur akan dipotong presisi sesuai total menit yang diajukan.</span>
                            </div>

                            <!-- Policy Info Badge -->
                            <div class="md:col-span-2 bg-blue-50/80 border border-blue-200 rounded-2xl p-4 flex items-start sm:items-center gap-3 text-xs text-blue-900">
                                <div class="p-2 bg-blue-500/10 text-blue-600 rounded-xl shrink-0">
                                    <i data-lucide="info" class="w-4 h-4"></i>
                                </div>
                                <div class="leading-relaxed text-[11px] sm:text-xs">
                                    <strong>Ketentuan Simpanan Lembur:</strong> 
                                    1 Hari Libur Kompensasi = 9 Jam Lembur &bull; Izin Pulang Cepat / Datang Terlambat = Maksimal 3 Jam.
                                </div>
                            </div>
                        @endif

                        <!-- File Pendukung -->
                        <div class="md:col-span-2">
                            <label for="file_pendukung" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                File Lampiran / Surat Bukti <span class="text-slate-400 font-normal text-xs">(Opsional)</span>
                            </label>
                            <input type="file" name="file_pendukung" id="file_pendukung" 
                                   class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 p-2.5 bg-slate-50 border text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                            <span class="text-[11px] text-slate-500 mt-1 block">Format diperbolehkan: PDF, JPG, PNG (Maksimal 2MB)</span>
                            @error('file_pendukung') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alasan (Opsional) -->
                        <div class="md:col-span-2">
                            <label for="alasan" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5">
                                Alasan / Keperluan Cuti <span class="text-slate-400 font-normal text-xs">(Opsional)</span>
                            </label>
                            <textarea name="alasan" id="alasan" rows="3" 
                                      placeholder="Tuliskan alasan atau keperluan cuti..." 
                                      class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 p-3.5 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-medium">{{ old('alasan') }}</textarea>
                            @error('alasan') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Button Toolbar -->
                <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 border-t border-slate-100">
                    <button type="reset" class="px-5 py-3.5 rounded-2xl border border-slate-300 text-slate-700 text-xs sm:text-sm font-bold hover:bg-slate-100 transition-colors text-center">
                        Reset Formulir
                    </button>
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white rounded-2xl font-display font-bold shadow-xl shadow-blue-600/30 hover:shadow-2xl transition-all duration-200 flex items-center justify-center gap-2.5 text-xs sm:text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pengajuan Cuti Sekarang</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleIzinJam(jenis) {
        const container = document.getElementById('durasiJamContainer');
        if (!container) return;
        if (jenis === 'Izin Pulang Cepat' || jenis === 'Izin Datang Terlambat') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function updatePegawaiInfo(select) {
        const option = select.options[select.selectedIndex];
        if (!option.value) return;

        const nip = option.getAttribute('data-nip');
        const divisi = option.getAttribute('data-divisi');
        const sisa = option.getAttribute('data-sisa');
        const lembur = option.getAttribute('data-lembur') || '0 Menit';

        document.getElementById('preview-nip').innerText = nip;
        document.getElementById('preview-divisi').innerText = divisi;
        document.getElementById('preview-sisa').innerText = sisa + ' Hari';
        
        const previewLembur = document.getElementById('preview-lembur');
        if (previewLembur) {
            previewLembur.innerText = lembur;
        }

        document.getElementById('pegawai-detail-card').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('pegawai_id');
        if (select && select.value) {
            updatePegawaiInfo(select);
        }

        const jenisSelect = document.getElementById('jenis_cuti');
        if (jenisSelect && jenisSelect.value) {
            toggleIzinJam(jenisSelect.value);
        }
    });
</script>
@endpush
