@extends('layouts.public')

@section('title', 'Form Pengajuan Cuti Publik')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-2xl p-8 shadow-xl mb-8 border border-indigo-700/40 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none">
            <i data-lucide="file-text" class="w-64 h-64"></i>
        </div>
        <div class="relative z-10">
            <span class="px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold uppercase tracking-wider rounded-full inline-block mb-3">
                Akses Publik (Tanpa Login)
            </span>
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Formulir Pengajuan Cuti Pegawai</h1>
            <p class="text-blue-200 text-sm max-w-2xl">
                STIKes Panti Waluya Malang. Silakan lengkapi data pengajuan cuti Anda. Pengajuan ini akan diproses bertingkat oleh <strong>Kepala Divisi/Kaprodi</strong> &rarr; <strong>HRD</strong> &rarr; <strong>Ketua STIKes</strong>.
            </p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 sm:p-8">
        @if($pegawais->isEmpty())
            <div class="p-6 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-center">
                <i data-lucide="alert-circle" class="w-10 h-10 mx-auto text-amber-600 mb-2"></i>
                <p class="font-semibold text-lg">Belum Ada Data Pegawai</p>
                <p class="text-sm text-amber-700 mt-1">Data pegawai belum diinput ke sistem oleh HRD. Silakan hubungi bagian Kepegawaian STIKes Panti Waluya.</p>
            </div>
        @else
            <form action="{{ route('public.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section 1: Data Pemohon -->
                <div class="border-b border-slate-200 pb-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-extrabold">1</span>
                        Identitas Pegawai Pemohon
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pegawai Selection -->
                        <div class="md:col-span-2">
                            <label for="pegawai_id" class="block text-sm font-semibold text-slate-700 mb-1">
                                Pilih Pegawai / Pengaju Cuti <span class="text-rose-500">*</span>
                            </label>
                            <select name="pegawai_id" id="pegawai_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 text-slate-900 border font-medium" required onchange="updatePegawaiInfo(this)">
                                <option value="" disabled selected>-- Pilih Nama / NIP Pegawai --</option>
                                @foreach($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}" 
                                            data-nip="{{ $pegawai->nip }}"
                                            data-divisi="{{ $pegawai->divisi->nama_divisi ?? '-' }}"
                                            data-jabatan="{{ $pegawai->jabatan }}"
                                            data-sisa="{{ $pegawai->sisa_cuti }}"
                                            {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }} (NIP: {{ $pegawai->nip }}) - {{ $pegawai->divisi->nama_divisi ?? 'Tanpa Divisi' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pegawai_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Info Card Preview -->
                        <div id="pegawai-detail-card" class="hidden md:col-span-2 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                                <div>
                                    <span class="text-slate-500 block font-medium">NIP</span>
                                    <span id="preview-nip" class="font-bold text-slate-900 text-sm"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-medium">Divisi / Prodi</span>
                                    <span id="preview-divisi" class="font-bold text-slate-900 text-sm"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-medium">Jabatan</span>
                                    <span id="preview-jabatan" class="font-bold text-slate-900 text-sm"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-medium">Sisa Cuti Tahunan</span>
                                    <span id="preview-sisa" class="font-bold text-emerald-700 text-base"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Details Cuti -->
                <div class="border-b border-slate-200 pb-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-extrabold">2</span>
                        Detail Cuti yang Diajukan
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jenis Cuti -->
                        <div>
                            <label for="jenis_cuti" class="block text-sm font-semibold text-slate-700 mb-1">
                                Jenis Cuti <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_cuti" id="jenis_cuti" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium" required>
                                <option value="Cuti Tahunan" {{ old('jenis_cuti') == 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                                <option value="Cuti Sakit" {{ old('jenis_cuti') == 'Cuti Sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                                <option value="Cuti Melahirkan" {{ old('jenis_cuti') == 'Cuti Melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                                <option value="Cuti Alasan Penting" {{ old('jenis_cuti') == 'Cuti Alasan Penting' ? 'selected' : '' }}>Cuti Alasan Penting</option>
                                <option value="Cuti Besar" {{ old('jenis_cuti') == 'Cuti Besar' ? 'selected' : '' }}>Cuti Besar</option>
                            </select>
                        </div>

                        <!-- File Pendukung -->
                        <div>
                            <label for="file_pendukung" class="block text-sm font-semibold text-slate-700 mb-1">
                                File Pendukung (Lampiran Surat Dokter / Bukti, jika ada)
                            </label>
                            <input type="file" name="file_pendukung" id="file_pendukung" 
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 bg-slate-50 border text-xs text-slate-600">
                            <span class="text-[11px] text-slate-500">Format: PDF, JPG, PNG (Maks 2MB)</span>
                            @error('file_pendukung') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-semibold text-slate-700 mb-1">
                                Tanggal Mulai Cuti <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" 
                                   value="{{ old('tanggal_mulai', date('Y-m-d')) }}" 
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium" required onchange="calculateDays()">
                            @error('tanggal_mulai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tanggal Selesai -->
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-semibold text-slate-700 mb-1">
                                Tanggal Selesai Cuti <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" 
                                   value="{{ old('tanggal_selesai', date('Y-m-d')) }}" 
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium" required onchange="calculateDays()">
                            @error('tanggal_selesai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Durasi Days Indicator -->
                        <div class="md:col-span-2 bg-slate-100 rounded-xl p-3 flex items-center justify-between text-xs sm:text-sm font-medium text-slate-700">
                            <span>Estimasi Durasi Pengajuan Cuti:</span>
                            <span id="durasi-info" class="font-bold text-blue-700 text-base">1 Hari</span>
                        </div>

                        <!-- Alasan -->
                        <div class="md:col-span-2">
                            <label for="alasan" class="block text-sm font-semibold text-slate-700 mb-1">
                                Alasan / Keperluan Cuti <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="alasan" id="alasan" rows="3" 
                                      placeholder="Jelaskan keperluan pengajuan cuti secara singkat dan jelas..." 
                                      class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium" required>{{ old('alasan') }}</textarea>
                            @error('alasan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alamat Selama Cuti -->
                        <div>
                            <label for="alamat_cuti" class="block text-sm font-semibold text-slate-700 mb-1">
                                Alamat Selama Menjalankan Cuti <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="alamat_cuti" id="alamat_cuti" 
                                   placeholder="Contoh: Jl. Raya Langsep No. 45, Malang" 
                                   value="{{ old('alamat_cuti') }}" 
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium" required>
                            @error('alamat_cuti') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- No HP Selama Cuti -->
                        <div>
                            <label for="no_hp_cuti" class="block text-sm font-semibold text-slate-700 mb-1">
                                No. Telepon / WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="no_hp_cuti" id="no_hp_cuti" 
                                   placeholder="08123456789" 
                                   value="{{ old('no_hp_cuti') }}" 
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium" required>
                            @error('no_hp_cuti') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-4">
                    <button type="reset" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition-colors">
                        Reset Form
                    </button>
                    <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-blue-900 to-indigo-900 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center gap-2 text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pengajuan Cuti</span>
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
        const sisa = option.getAttribute('data-sisa');

        document.getElementById('preview-nip').innerText = nip;
        document.getElementById('preview-divisi').innerText = divisi;
        document.getElementById('preview-jabatan').innerText = jabatan;
        document.getElementById('preview-sisa').innerText = sisa + ' Hari';

        document.getElementById('pegawai-detail-card').classList.remove('hidden');
    }

    function calculateDays() {
        const startVal = document.getElementById('tanggal_mulai').value;
        const endVal = document.getElementById('tanggal_selesai').value;

        if (startVal && endVal) {
            const start = new Date(startVal);
            const end = new Date(endVal);

            if (end >= start) {
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('durasi-info').innerText = diffDays + ' Hari Kerja';
            } else {
                document.getElementById('durasi-info').innerText = 'Tanggal tidak valid';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('pegawai_id');
        if (select && select.value) {
            updatePegawaiInfo(select);
        }
        calculateDays();
    });
</script>
@endpush
