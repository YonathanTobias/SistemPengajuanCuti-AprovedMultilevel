@extends('layouts.public')

@section('title', 'Lacak Status Pengajuan Cuti')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 sm:p-8 mb-8 text-center">
        <div class="w-14 h-14 bg-blue-100 text-blue-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i data-lucide="search" class="w-8 h-8"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Lacak Status Pengajuan Cuti</h1>
        <p class="text-slate-600 text-sm max-w-xl mx-auto mb-6">
            Masukkan Kode Tracking (contoh: <span class="font-mono text-blue-700 bg-blue-50 px-2 py-0.5 rounded">CUTI-20260803-XXXX</span>) atau NIP Pegawai untuk memantau proses persetujuan.
        </p>

        <!-- Search Form -->
        <form action="{{ route('public.tracking') }}" method="GET" class="max-w-xl mx-auto flex gap-2">
            <input type="text" name="kode" value="{{ $search }}" 
                   placeholder="Masukkan Kode Tracking atau NIP Pegawai..." 
                   class="flex-1 rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-slate-50 border text-slate-900 font-medium text-sm" required>
            <button type="submit" class="px-6 py-3 bg-blue-900 hover:bg-blue-800 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all text-sm flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Cari</span>
            </button>
        </form>
    </div>

    <!-- Search Result Single Detail -->
    @if($cuti)
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-8">
            <!-- Header Bar -->
            <div class="bg-slate-900 text-white p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs text-blue-300 font-mono tracking-wider">KODE TRACKING</span>
                    <h2 class="text-2xl font-mono font-bold text-white">{{ $cuti->kode_tracking }}</h2>
                    <span class="text-xs text-slate-400">Diajukan pada: {{ $cuti->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
                <div>
                    @if($cuti->status === 'pending_kadiv')
                        <span class="px-4 py-2 bg-amber-500/20 text-amber-300 border border-amber-500/40 rounded-full text-xs font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            Menunggu Approval Kadiv / Kaprodi
                        </span>
                    @elseif($cuti->status === 'pending_hrd')
                        <span class="px-4 py-2 bg-blue-500/20 text-blue-300 border border-blue-500/40 rounded-full text-xs font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                            Menunggu Approval HRD
                        </span>
                    @elseif($cuti->status === 'pending_ketua')
                        <span class="px-4 py-2 bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 rounded-full text-xs font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                            Menunggu Approval Ketua STIKes
                        </span>
                    @elseif($cuti->status === 'approved')
                        <span class="px-4 py-2 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 rounded-full text-xs font-bold flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                            Disetujui Sepenuhnya (Approved)
                        </span>
                    @elseif($cuti->status === 'rejected')
                        <span class="px-4 py-2 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-full text-xs font-bold flex items-center gap-1.5">
                            <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i>
                            Pengajuan Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Approval Progress Stepper Graphic -->
            <div class="p-6 sm:p-8 bg-slate-50 border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-6 text-center">Tahapan Verifikasi & Persetujuan Multi-Level</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                    <!-- Step 1: Kadiv -->
                    <div class="bg-white p-5 rounded-xl border {{ in_array($cuti->status, ['pending_hrd', 'pending_ketua', 'approved']) ? 'border-emerald-300 shadow-emerald-50 shadow-sm' : ($cuti->status === 'pending_kadiv' ? 'border-amber-400 shadow-md ring-2 ring-amber-100' : ($cuti->status === 'rejected' && $cuti->rejected_by === 'kadiv' ? 'border-rose-300 bg-rose-50' : 'border-slate-200')) }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold px-2 py-0.5 rounded {{ in_array($cuti->status, ['pending_hrd', 'pending_ketua', 'approved']) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                LEVEL 1
                            </span>
                            @if(in_array($cuti->status, ['pending_hrd', 'pending_ketua', 'approved']))
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                            @elseif($cuti->status === 'pending_kadiv')
                                <i data-lucide="clock" class="w-5 h-5 text-amber-500 animate-spin"></i>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'kadiv')
                                <i data-lucide="x-circle" class="w-5 h-5 text-rose-600"></i>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Kepala Divisi / Kaprodi</h4>
                        <p class="text-xs text-slate-500 mt-1">Divisi: {{ $cuti->pegawai->divisi->nama_divisi ?? '-' }}</p>
                        <div class="mt-3 pt-3 border-t border-slate-100 text-xs">
                            @if($cuti->kadiv_approved_at)
                                <p class="text-emerald-700 font-semibold">&check; Disetujui: {{ $cuti->kadiv_approved_at->format('d/m/Y H:i') }}</p>
                                <p class="text-slate-600 italic mt-1">"{{ $cuti->catatan_kadiv }}"</p>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'kadiv')
                                <p class="text-rose-700 font-semibold">&cross; Ditolak di tahap ini</p>
                            @else
                                <p class="text-amber-700 font-medium">Menunggu evaluasi Kadiv...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 2: HRD -->
                    <div class="bg-white p-5 rounded-xl border {{ in_array($cuti->status, ['pending_ketua', 'approved']) ? 'border-emerald-300 shadow-emerald-50 shadow-sm' : ($cuti->status === 'pending_hrd' ? 'border-blue-400 shadow-md ring-2 ring-blue-100' : ($cuti->status === 'rejected' && $cuti->rejected_by === 'hrd' ? 'border-rose-300 bg-rose-50' : 'border-slate-200')) }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold px-2 py-0.5 rounded {{ in_array($cuti->status, ['pending_ketua', 'approved']) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                LEVEL 2
                            </span>
                            @if(in_array($cuti->status, ['pending_ketua', 'approved']))
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                            @elseif($cuti->status === 'pending_hrd')
                                <i data-lucide="clock" class="w-5 h-5 text-blue-500 animate-spin"></i>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'hrd')
                                <i data-lucide="x-circle" class="w-5 h-5 text-rose-600"></i>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Tim HRD & Kepegawaian</h4>
                        <p class="text-xs text-slate-500 mt-1">Verifikasi Kuota & Berkas</p>
                        <div class="mt-3 pt-3 border-t border-slate-100 text-xs">
                            @if($cuti->hrd_approved_at)
                                <p class="text-emerald-700 font-semibold">&check; Disetujui: {{ $cuti->hrd_approved_at->format('d/m/Y H:i') }}</p>
                                <p class="text-slate-600 italic mt-1">"{{ $cuti->catatan_hrd }}"</p>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'hrd')
                                <p class="text-rose-700 font-semibold">&cross; Ditolak di tahap ini</p>
                            @else
                                <p class="text-slate-400 font-medium">Menunggu persetujuan Level 1</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 3: Ketua STIKes -->
                    <div class="bg-white p-5 rounded-xl border {{ $cuti->status === 'approved' ? 'border-emerald-500 shadow-emerald-50 shadow-md ring-2 ring-emerald-200' : ($cuti->status === 'pending_ketua' ? 'border-indigo-400 shadow-md ring-2 ring-indigo-100' : ($cuti->status === 'rejected' && $cuti->rejected_by === 'ketua' ? 'border-rose-300 bg-rose-50' : 'border-slate-200')) }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold px-2 py-0.5 rounded {{ $cuti->status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                                LEVEL 3 (FINAL)
                            </span>
                            @if($cuti->status === 'approved')
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                            @elseif($cuti->status === 'pending_ketua')
                                <i data-lucide="clock" class="w-5 h-5 text-indigo-500 animate-spin"></i>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'ketua')
                                <i data-lucide="x-circle" class="w-5 h-5 text-rose-600"></i>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Ketua STIKes Panti Waluya</h4>
                        <p class="text-xs text-slate-500 mt-1">Persetujuan Akhir Terbit Surat</p>
                        <div class="mt-3 pt-3 border-t border-slate-100 text-xs">
                            @if($cuti->ketua_approved_at)
                                <p class="text-emerald-700 font-semibold">&check; Disetujui: {{ $cuti->ketua_approved_at->format('d/m/Y H:i') }}</p>
                                <p class="text-slate-600 italic mt-1">"{{ $cuti->catatan_ketua }}"</p>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'ketua')
                                <p class="text-rose-700 font-semibold">&cross; Ditolak di tahap ini</p>
                            @else
                                <p class="text-slate-400 font-medium">Menunggu persetujuan Level 2</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information Table -->
            <div class="p-6 sm:p-8 space-y-6">
                @if($cuti->status === 'rejected')
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-900">
                        <h4 class="font-bold text-sm flex items-center gap-2 text-rose-800">
                            <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-600"></i>
                            Catatan Penolakan Pengajuan Cuti:
                        </h4>
                        <p class="text-sm mt-1 font-medium">{{ $cuti->catatan_penolakan ?: 'Tidak ada alasan khusus.' }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 border-b pb-2 text-sm">Informasi Pemohon</h4>
                        <div class="text-xs space-y-2">
                            <div class="flex justify-between"><span class="text-slate-500">Nama Pegawai:</span> <span class="font-semibold text-slate-900">{{ $cuti->pegawai->nama }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">NIP:</span> <span class="font-mono font-semibold text-slate-900">{{ $cuti->pegawai->nip }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Divisi / Prodi:</span> <span class="font-semibold text-slate-900">{{ $cuti->pegawai->divisi->nama_divisi ?? '-' }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Jabatan:</span> <span class="font-semibold text-slate-900">{{ $cuti->pegawai->jabatan }}</span></div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 border-b pb-2 text-sm">Informasi Pengajuan Cuti</h4>
                        <div class="text-xs space-y-2">
                            <div class="flex justify-between"><span class="text-slate-500">Jenis Cuti:</span> <span class="font-semibold text-blue-700">{{ $cuti->jenis_cuti }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Tanggal Cuti:</span> <span class="font-semibold text-slate-900">{{ $cuti->tanggal_mulai->translatedFormat('d M Y') }} s/d {{ $cuti->tanggal_selesai->translatedFormat('d M Y') }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Jumlah Durasi:</span> <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">{{ $cuti->jumlah_hari }} Hari Kerja</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Alamat Cuti:</span> <span class="font-medium text-slate-900">{{ $cuti->alamat_cuti }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">No. HP Aktif:</span> <span class="font-medium text-slate-900">{{ $cuti->no_hp_cuti }}</span></div>
                            @if($cuti->file_pendukung)
                                <div class="flex justify-between items-center pt-1"><span class="text-slate-500">File Lampiran:</span> <a href="{{ asset($cuti->file_pendukung) }}" target="_blank" class="text-blue-600 font-bold underline flex items-center gap-1"><i data-lucide="paperclip" class="w-3.5 h-3.5"></i> Lihat Berkas</a></div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Button for Approved Leave -->
                @if($cuti->status === 'approved')
                    <div class="pt-6 border-t border-slate-200 flex justify-center">
                        <a href="{{ route('public.surat', $cuti->kode_tracking) }}" target="_blank"
                           class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center gap-2 text-sm">
                            <i data-lucide="printer" class="w-5 h-5"></i>
                            <span>Cetak Surat Izin Cuti Resmi (PDF / Print)</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @elseif($cutiList->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-8">
            <h3 class="font-bold text-slate-900 text-lg mb-4">Hasil Pencarian Cuti Pegawai</h3>
            <div class="divide-y divide-slate-100">
                @foreach($cutiList as $item)
                    <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-slate-50 p-2 rounded-xl transition-colors">
                        <div>
                            <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $item->kode_tracking }}</span>
                            <h4 class="font-bold text-slate-900 text-sm mt-1">{{ $item->pegawai->nama }} - {{ $item->jenis_cuti }}</h4>
                            <p class="text-xs text-slate-500">{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }} ({{ $item->jumlah_hari }} hari)</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('public.tracking', ['kode' => $item->kode_tracking]) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-1.5">
                                <span>Detail Status</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($search)
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-8 text-center">
            <i data-lucide="search-x" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
            <h3 class="font-bold text-slate-800 text-lg">Pengajuan Cuti Tidak Ditemukan</h3>
            <p class="text-slate-500 text-sm mt-1">Tidak ada data cuti dengan Kode Tracking atau NIP "<strong>{{ $search }}</strong>". Mohon periksa kembali input Anda.</p>
        </div>
    @endif
</div>
@endsection
