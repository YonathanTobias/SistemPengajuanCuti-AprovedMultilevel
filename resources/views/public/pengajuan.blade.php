@extends('layouts.public')

@section('title', 'Form Pengajuan Cuti & Izin Jam Lembur')

@section('content')
<div class="max-w-4xl mx-auto w-full overflow-x-hidden">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-2xl p-5 sm:p-8 shadow-xl mb-6 sm:mb-8 border border-indigo-700/40 relative overflow-hidden">
        <div class="relative z-10">
            <span class="px-2.5 py-0.5 bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] sm:text-xs font-semibold uppercase tracking-wider rounded-full inline-block mb-2">
                Akses Publik (Cuti &amp; Izin Jam Lembur)
            </span>
            <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-white mb-2 leading-tight">Formulir Pengajuan Cuti &amp; Izin Pegawai</h1>
            <p class="text-blue-200 text-xs sm:text-sm max-w-2xl leading-relaxed">
                STIKes Panti Waluya Malang. Pengajuan cuti harian maupun <strong>izin pulang cepat / datang terlambat (maks. 3 jam)</strong> menggunakan saldo jam lembur.
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
            <form action="{{ route('public.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-full">
                @csrf

                <!-- Section 1: Data Pemohon -->
                <div class="border-b border-slate-200 pb-6">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-extrabold shrink-0">1</span>
                        Identitas Pegawai Pemohon
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Pegawai Selection -->
                        <div class="md:col-span-2 w-full max-w-full overflow-hidden">
                            <label for="pegawai_id" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Pilih Pegawai / Pengaju <span class="text-rose-500">*</span>
                            </label>
                            <select name="pegawai_id" id="pegawai_id" class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 sm:p-3 bg-slate-50 text-slate-900 border text-xs sm:text-sm font-medium truncate" required onchange="updatePegawaiInfo(this)">
                                <option value="" disabled selected>-- Pilih Nama / NIP Pegawai --</option>
                                @foreach($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}" 
                                            data-nip="{{ $pegawai->nip }}"
                                            data-divisi="{{ $pegawai->divisi->nama_divisi ?? '-' }}"
                                            data-jabatan="{{ $pegawai->jabatan }}"
                                            data-sisa="{{ $pegawai->sisa_cuti }}"
                                            data-lembur="{{ $pegawai->saldo_lembur_formatted }}"
                                            {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }} ({{ $pegawai->nip }}) - {{ $pegawai->divisi->nama_divisi ?? 'Tanpa Divisi' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pegawai_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Info Card Preview -->
                        <div id="pegawai-detail-card" class="hidden md:col-span-2 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
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
                                    <span class="text-slate-500 block font-medium text-[11px]">Sisa Cuti Tahunan</span>
                                    <span id="preview-sisa" class="font-bold text-emerald-700 text-sm sm:text-base"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-medium text-[11px]">Saldo Jam Lembur</span>
                                    <span id="preview-lembur" class="font-bold text-amber-700 text-sm sm:text-base"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Details Cuti / Izin -->
                <div class="border-b border-slate-200 pb-6">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-extrabold shrink-0">2</span>
                        Detail Pengajuan Cuti / Izin Jam Lembur
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Tanggal Pelaksanaan -->
                        <div class="w-full">
                            <label for="tanggal_cuti" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_cuti" id="tanggal_cuti" 
                                   value="{{ old('tanggal_cuti', date('Y-m-d')) }}" 
                                   class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 sm:p-3 bg-slate-50 border text-slate-900 text-xs sm:text-sm font-bold" required>
                            <span class="text-[10px] sm:text-[11px] text-slate-500 mt-1 block">Tentukan 1 tanggal pelaksanaan cuti/izin ini</span>
                            @error('tanggal_cuti') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jenis Cuti / Izin -->
                        <div class="w-full">
                            <label for="jenis_cuti" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Jenis Cuti / Izin <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_cuti" id="jenis_cuti" onchange="toggleIzinJam(this.value)" class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 sm:p-3 bg-slate-50 border text-slate-900 text-xs sm:text-sm font-medium" required>
                                <option value="Cuti Tahunan" {{ old('jenis_cuti') == 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan (Potong 1 Hari Kuota)</option>
                                <option value="Cuti Kompensasi Lembur" {{ old('jenis_cuti') == 'Cuti Kompensasi Lembur' ? 'selected' : '' }}>Cuti Kompensasi Lembur (Tukar 9 Jam Lembur = 1 Hari Libur)</option>
                                <option value="Izin Pulang Cepat" {{ old('jenis_cuti') == 'Izin Pulang Cepat' ? 'selected' : '' }}>Izin Pulang Cepat (Potong Saldo Lembur, Maks. 3 Jam)</option>
                                <option value="Izin Datang Terlambat" {{ old('jenis_cuti') == 'Izin Datang Terlambat' ? 'selected' : '' }}>Izin Datang Terlambat (Potong Saldo Lembur, Maks. 3 Jam)</option>
                                <option value="Cuti Sakit" {{ old('jenis_cuti') == 'Cuti Sakit' ? 'selected' : '' }}>Cuti Sakit (1 Hari)</option>
                                <option value="Cuti Melahirkan" {{ old('jenis_cuti') == 'Cuti Melahirkan' ? 'selected' : '' }}>Cuti Melahirkan (1 Hari)</option>
                                <option value="Cuti Alasan Penting" {{ old('jenis_cuti') == 'Cuti Alasan Penting' ? 'selected' : '' }}>Cuti Alasan Penting (1 Hari)</option>
                                <option value="Cuti Besar" {{ old('jenis_cuti') == 'Cuti Besar' ? 'selected' : '' }}>Cuti Besar (1 Hari)</option>
                            </select>
                        </div>

                        <!-- Opsi Durasi Jam & Menit (Muncul jika Pulang Cepat / Datang Terlambat) -->
                        <div id="durasiJamContainer" class="hidden md:col-span-2 bg-amber-50/80 border border-amber-200 rounded-xl p-4 space-y-3">
                            <label class="block text-xs font-bold text-amber-900 uppercase">
                                Tentukan Durasi Izin (Maksimal 3 Jam) <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="izin_jam" class="block text-[11px] font-semibold text-slate-700 mb-1">Jumlah Jam</label>
                                    <select name="izin_jam" id="izin_jam" class="w-full rounded-xl border-amber-300 p-2.5 bg-white text-slate-900 text-xs sm:text-sm font-bold focus:ring-amber-500">
                                        <option value="0">0 Jam</option>
                                        <option value="1" selected>1 Jam</option>
                                        <option value="2">2 Jam</option>
                                        <option value="3">3 Jam (Maksimal)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="izin_menit" class="block text-[11px] font-semibold text-slate-700 mb-1">Tambahan Menit</label>
                                    <select name="izin_menit" id="izin_menit" class="w-full rounded-xl border-amber-300 p-2.5 bg-white text-slate-900 text-xs sm:text-sm font-bold focus:ring-amber-500">
                                        <option value="0" selected>0 Menit</option>
                                        <option value="15">15 Menit</option>
                                        <option value="20">20 Menit</option>
                                        <option value="30">30 Menit (Setengah Jam)</option>
                                        <option value="45">45 Menit</option>
                                    </select>
                                </div>
                            </div>
                            <span class="text-[11px] text-amber-800 block">Contoh: 1 Jam 30 Menit, 45 Menit, atau 2 Jam. Saldo lembur akan dipotong presisi sesuai menit yang diambil.</span>
                        </div>

                        <!-- Policy Info Badge -->
                        <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex items-start sm:items-center gap-3 text-xs text-blue-900">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5 sm:mt-0"></i>
                            <div class="leading-relaxed text-[11px] sm:text-xs">
                                <strong>Ketentuan Pengambilan:</strong> 
                                1 Hari Libur Kompensasi = 9 Jam Lembur &bull; Izin Pulang Cepat / Datang Terlambat = Maksimal 3 Jam dipotong presisi dari Saldo Lembur.
                            </div>
                        </div>

                        <!-- File Pendukung -->
                        <div class="md:col-span-2 w-full max-w-full overflow-hidden">
                            <label for="file_pendukung" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                File Pendukung (Lampiran Surat Dokter / Bukti, opsional)
                            </label>
                            <input type="file" name="file_pendukung" id="file_pendukung" 
                                   class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 bg-slate-50 border text-xs text-slate-600">
                            <span class="text-[10px] sm:text-[11px] text-slate-500">Format: PDF, JPG, PNG (Maks 2MB)</span>
                            @error('file_pendukung') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alasan (Opsional) -->
                        <div class="md:col-span-2 w-full">
                            <label for="alasan" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
                                Alasan / Keperluan <span class="text-slate-400 font-normal text-xs">(Opsional)</span>
                            </label>
                            <textarea name="alasan" id="alasan" rows="3" 
                                      placeholder="Jelaskan keperluan pengajuan cuti/izin (opsional)..." 
                                      class="w-full max-w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 text-xs sm:text-sm font-medium">{{ old('alasan') }}</textarea>
                            @error('alasan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <button type="reset" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors text-center">
                        Reset Form
                    </button>
                    <button type="submit" class="px-6 py-3.5 bg-gradient-to-r from-blue-900 to-indigo-900 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 text-xs sm:text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pengajuan</span>
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
        const jabatan = option.getAttribute('data-jabatan');
        const sisa = option.getAttribute('data-sisa');
        const lembur = option.getAttribute('data-lembur') || '0 Menit';

        document.getElementById('preview-nip').innerText = nip;
        document.getElementById('preview-divisi').innerText = divisi;
        document.getElementById('preview-jabatan').innerText = jabatan;
        document.getElementById('preview-sisa').innerText = sisa + ' Hari';
        document.getElementById('preview-lembur').innerText = lembur;

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
