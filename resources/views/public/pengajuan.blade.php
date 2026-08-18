@extends('layouts.public')

@section('title', 'Form Pengajuan Cuti Publik (Per 1 Hari)')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-2xl p-8 shadow-xl mb-8 border border-indigo-700/40 relative overflow-hidden">
        <div class="relative z-10">
            <span class="px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold uppercase tracking-wider rounded-full inline-block mb-3">
                Akses Publik (Per 1 Hari Cuti)
            </span>
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Formulir Pengajuan Cuti Pegawai</h1>
            <p class="text-blue-200 text-sm max-w-2xl">
                STIKes Panti Waluya Malang. Setiap formulir pengajuan berlaku untuk <strong>1 (satu) hari cuti</strong>. Jika membutuhkan cuti beberapa hari, silakan ajukan formulir kembali untuk masing-masing tanggal.
            </p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 sm:p-8">
        @if($pegawais->isEmpty())
            <div class="p-6 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-center">
                <i data-lucide="alert-circle" class="w-10 h-10 mx-auto text-amber-600 mb-2"></i>
                <p class="font-semibold text-lg">Belum Ada Data Pegawai</p>
                <p class="text-sm text-amber-700 mt-1">Data pegawai belum diinput ke sistem oleh HRD.</p>
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

                <!-- Section 2: Details Cuti 1 Hari -->
                <div class="border-b border-slate-200 pb-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-extrabold">2</span>
                        Detail Pengajuan Cuti (1 Hari)
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal Cuti (Single Date) -->
                        <div>
                            <label for="tanggal_cuti" class="block text-sm font-semibold text-slate-700 mb-1">
                                Tanggal Pelaksanaan Cuti <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_cuti" id="tanggal_cuti" 
                                   value="{{ old('tanggal_cuti', date('Y-m-d')) }}" 
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-bold" required>
                            <span class="text-[11px] text-slate-500 mt-1 block">Tentukan 1 tanggal spesifik untuk hari cuti ini</span>
                            @error('tanggal_cuti') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jenis Cuti -->
                        <div>
                            <label for="jenis_cuti" class="block text-sm font-semibold text-slate-700 mb-1">
                                Jenis Cuti <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_cuti" id="jenis_cuti" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium" required>
                                <option value="Cuti Tahunan" {{ old('jenis_cuti') == 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan (1 Hari)</option>
                                <option value="Cuti Sakit" {{ old('jenis_cuti') == 'Cuti Sakit' ? 'selected' : '' }}>Cuti Sakit (1 Hari)</option>
                                <option value="Cuti Melahirkan" {{ old('jenis_cuti') == 'Cuti Melahirkan' ? 'selected' : '' }}>Cuti Melahirkan (1 Hari)</option>
                                <option value="Cuti Alasan Penting" {{ old('jenis_cuti') == 'Cuti Alasan Penting' ? 'selected' : '' }}>Cuti Alasan Penting (1 Hari)</option>
                                <option value="Cuti Besar" {{ old('jenis_cuti') == 'Cuti Besar' ? 'selected' : '' }}>Cuti Besar (1 Hari)</option>
                            </select>
                        </div>

                        <!-- Policy Info Badge -->
                        <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex items-center gap-3 text-xs text-blue-900">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0"></i>
                            <div>
                                <strong>Ketentuan 1 Hari Cuti:</strong> Setiap pengajuan ini memotong 1 hari kuota cuti. Jika mengajukan cuti 3 hari berturut-turut, silakan kirimkan formulir ini sebanyak 3 kali sesuai tanggal yang diinginkan.
                            </div>
                        </div>

                        <!-- File Pendukung -->
                        <div class="md:col-span-2">
                            <label for="file_pendukung" class="block text-sm font-semibold text-slate-700 mb-1">
                                File Pendukung (Lampiran Surat Dokter / Bukti, opsional)
                            </label>
                            <input type="file" name="file_pendukung" id="file_pendukung" 
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 bg-slate-50 border text-xs text-slate-600">
                            <span class="text-[11px] text-slate-500">Format: PDF, JPG, PNG (Maks 2MB)</span>
                            @error('file_pendukung') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alasan (Opsional) -->
                        <div class="md:col-span-2">
                            <label for="alasan" class="block text-sm font-semibold text-slate-700 mb-1">
                                Alasan / Keperluan Cuti <span class="text-slate-400 font-normal text-xs">(Opsional)</span>
                            </label>
                            <textarea name="alasan" id="alasan" rows="3" 
                                      placeholder="Jelaskan keperluan pengajuan cuti (opsional)..." 
                                      class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium">{{ old('alasan') }}</textarea>
                            @error('alasan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
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
                        <span>Kirim Pengajuan Cuti (1 Hari)</span>
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

    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('pegawai_id');
        if (select && select.value) {
            updatePegawaiInfo(select);
        }
    });
</script>
@endpush
