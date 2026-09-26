@extends('layouts.public')

@section('title', 'Lacak Status Pengajuan Cuti')

@section('content')
<div class="max-w-4xl mx-auto w-full">
    
    <!-- Search Header Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-blue-950 to-slate-950 p-6 sm:p-10 text-white shadow-2xl border border-slate-800/80 mb-8 text-center">
        <!-- Ambient Mesh -->
        <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-96 h-48 bg-blue-600/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/15 text-blue-300 border border-blue-400/30 text-[11px] font-bold uppercase tracking-wider rounded-full mb-3 backdrop-blur-sm">
                <i data-lucide="radar" class="w-3.5 h-3.5 text-blue-400"></i>
                <span>Monitoring Status Realtime</span>
            </div>
            <h1 class="font-display text-2xl sm:text-4xl font-extrabold tracking-tight text-white mb-3">
                Lacak Status Pengajuan Cuti
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm max-w-lg mx-auto mb-6 leading-relaxed">
                Pantau proses verifikasi cuti berjenjang. Masukkan <strong class="text-white">Kode Tracking</strong> atau <strong class="text-white">NIP Pegawai</strong> Anda di bawah ini:
            </p>

            <!-- Search Input Box -->
            <form action="{{ route('public.tracking') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5 max-w-xl mx-auto">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </div>
                    <input type="text" name="kode" value="{{ $search }}" 
                           placeholder="Contoh: CUTI-20260102-XXXX atau NIP..." 
                           class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-slate-400 font-semibold text-xs sm:text-sm focus:bg-white focus:text-slate-900 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all backdrop-blur-md" required>
                </div>
                <button type="submit" class="px-7 py-3.5 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-display font-bold text-xs sm:text-sm shadow-xl shadow-blue-600/30 transition-all flex items-center justify-center gap-2 shrink-0">
                    <span>Lacak Sekarang</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Search Result Detail -->
    @if($cuti)
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden mb-8 text-slate-900 animate-in fade-in slide-in-from-bottom-3 duration-300">
            
            <!-- Result Top Banner -->
            <div class="bg-slate-950 text-white p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800">
                <div>
                    <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-blue-400">
                        <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                        <span>KODE TRACKING RESMI</span>
                    </div>
                    <h2 class="font-mono text-2xl sm:text-3xl font-black text-white mt-1">{{ $cuti->kode_tracking }}</h2>
                    <div class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Diajukan: {{ $cuti->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div>
                    @if($cuti->status === 'pending_kadiv')
                        <span class="px-4 py-2.5 bg-amber-500/15 text-amber-300 border border-amber-500/40 rounded-2xl text-xs font-bold flex items-center gap-2 backdrop-blur-md">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                            Menunggu Approval Kadiv / Kaprodi
                        </span>
                    @elseif($cuti->status === 'pending_hrd')
                        <span class="px-4 py-2.5 bg-blue-500/15 text-blue-300 border border-blue-500/40 rounded-2xl text-xs font-bold flex items-center gap-2 backdrop-blur-md">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400 animate-pulse"></span>
                            Menunggu Approval HRD
                        </span>
                    @elseif($cuti->status === 'pending_ketua')
                        <span class="px-4 py-2.5 bg-indigo-500/15 text-indigo-300 border border-indigo-500/40 rounded-2xl text-xs font-bold flex items-center gap-2 backdrop-blur-md">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 animate-pulse"></span>
                            Menunggu Approval Ketua STIKes
                        </span>
                    @elseif($cuti->status === 'approved')
                        <span class="px-4 py-2.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 rounded-2xl text-xs font-bold flex items-center gap-2 backdrop-blur-md">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                            Disetujui Sepenuhnya (Approved)
                        </span>
                    @elseif($cuti->status === 'rejected')
                        <span class="px-4 py-2.5 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-2xl text-xs font-bold flex items-center gap-2 backdrop-blur-md">
                            <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i>
                            Pengajuan Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Multi-Level Approval Timeline Stepper -->
            <div class="p-6 sm:p-8 bg-slate-50 border-b border-slate-200/80">
                <div class="text-center mb-6">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-white px-3 py-1 rounded-full border border-slate-200 inline-block shadow-sm">
                        Tahapan Persetujuan Berjenjang (3 Level)
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Step 1: Kadiv -->
                    <div class="bg-white p-5 rounded-2xl border transition-all duration-200 {{ in_array($cuti->status, ['pending_hrd', 'pending_ketua', 'approved']) ? 'border-emerald-300 shadow-lg shadow-emerald-50' : ($cuti->status === 'pending_kadiv' ? 'border-amber-400 ring-4 ring-amber-100 shadow-xl' : ($cuti->status === 'rejected' && $cuti->rejected_by === 'kadiv' ? 'border-rose-300 bg-rose-50' : 'border-slate-200 opacity-70')) }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-lg {{ in_array($cuti->status, ['pending_hrd', 'pending_ketua', 'approved']) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                LEVEL 1
                            </span>
                            @if(in_array($cuti->status, ['pending_hrd', 'pending_ketua', 'approved']))
                                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                </div>
                            @elseif($cuti->status === 'pending_kadiv')
                                <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center animate-spin">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                </div>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'kadiv')
                                <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <i data-lucide="x" class="w-3.5 h-3.5 stroke-[3]"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm">Kepala Divisi / Kaprodi</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Divisi: {{ $cuti->pegawai->divisi->nama_divisi ?? '-' }}</p>
                        <div class="mt-3 pt-3 border-t border-slate-100 text-xs">
                            @if($cuti->kadiv_approved_at)
                                <p class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                    Disetujui: {{ $cuti->kadiv_approved_at->format('d/m/Y H:i') }}
                                </p>
                                @if($cuti->catatan_kadiv)
                                    <p class="text-slate-600 italic mt-1 text-[11px]">"{{ $cuti->catatan_kadiv }}"</p>
                                @endif
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'kadiv')
                                <p class="text-rose-700 font-bold">&cross; Ditolak di tahap ini</p>
                            @else
                                <p class="text-amber-700 font-medium animate-pulse">Menunggu evaluasi Kadiv...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 2: HRD -->
                    <div class="bg-white p-5 rounded-2xl border transition-all duration-200 {{ in_array($cuti->status, ['pending_ketua', 'approved']) ? 'border-emerald-300 shadow-lg shadow-emerald-50' : ($cuti->status === 'pending_hrd' ? 'border-blue-400 ring-4 ring-blue-100 shadow-xl' : ($cuti->status === 'rejected' && $cuti->rejected_by === 'hrd' ? 'border-rose-300 bg-rose-50' : 'border-slate-200 opacity-70')) }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-lg {{ in_array($cuti->status, ['pending_ketua', 'approved']) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                LEVEL 2
                            </span>
                            @if(in_array($cuti->status, ['pending_ketua', 'approved']))
                                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                </div>
                            @elseif($cuti->status === 'pending_hrd')
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center animate-spin">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                </div>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'hrd')
                                <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <i data-lucide="x" class="w-3.5 h-3.5 stroke-[3]"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm">Tim HRD &amp; Kepegawaian</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Verifikasi Kuota &amp; Berkas</p>
                        <div class="mt-3 pt-3 border-t border-slate-100 text-xs">
                            @if($cuti->hrd_approved_at)
                                <p class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                    Disetujui: {{ $cuti->hrd_approved_at->format('d/m/Y H:i') }}
                                </p>
                                @if($cuti->catatan_hrd)
                                    <p class="text-slate-600 italic mt-1 text-[11px]">"{{ $cuti->catatan_hrd }}"</p>
                                @endif
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'hrd')
                                <p class="text-rose-700 font-bold">&cross; Ditolak di tahap ini</p>
                            @else
                                <p class="text-slate-400 font-medium">Menunggu persetujuan Level 1</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 3: Ketua STIKes -->
                    <div class="bg-white p-5 rounded-2xl border transition-all duration-200 {{ $cuti->status === 'approved' ? 'border-emerald-500 shadow-xl ring-4 ring-emerald-100' : ($cuti->status === 'pending_ketua' ? 'border-indigo-400 ring-4 ring-indigo-100 shadow-xl' : ($cuti->status === 'rejected' && $cuti->rejected_by === 'ketua' ? 'border-rose-300 bg-rose-50' : 'border-slate-200 opacity-70')) }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-lg {{ $cuti->status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                                LEVEL 3 (FINAL)
                            </span>
                            @if($cuti->status === 'approved')
                                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                </div>
                            @elseif($cuti->status === 'pending_ketua')
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center animate-spin">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                </div>
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'ketua')
                                <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <i data-lucide="x" class="w-3.5 h-3.5 stroke-[3]"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm">Ketua STIKes Panti Waluya</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Persetujuan Akhir Terbit Surat</p>
                        <div class="mt-3 pt-3 border-t border-slate-100 text-xs">
                            @if($cuti->ketua_approved_at)
                                <p class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                    Disetujui: {{ $cuti->ketua_approved_at->format('d/m/Y H:i') }}
                                </p>
                                @if($cuti->catatan_ketua)
                                    <p class="text-slate-600 italic mt-1 text-[11px]">"{{ $cuti->catatan_ketua }}"</p>
                                @endif
                            @elseif($cuti->status === 'rejected' && $cuti->rejected_by === 'ketua')
                                <p class="text-rose-700 font-bold">&cross; Ditolak di tahap ini</p>
                            @else
                                <p class="text-slate-400 font-medium">Menunggu persetujuan Level 2</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information Table & Actions -->
            <div class="p-6 sm:p-8 space-y-6">
                @if($cuti->status === 'rejected')
                    <div class="p-5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-900 flex items-start gap-3">
                        <i data-lucide="alert-octagon" class="w-6 h-6 text-rose-600 shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="font-bold text-sm text-rose-800">Catatan Penolakan Pengajuan Cuti:</h4>
                            <p class="text-xs sm:text-sm mt-1 font-medium">{{ $cuti->catatan_penolakan ?: 'Tidak ada catatan khusus.' }}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kolom Pemohon -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 space-y-3">
                        <h4 class="font-display font-bold text-slate-900 border-b border-slate-200 pb-2 text-sm flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                            <span>Informasi Pemohon</span>
                        </h4>
                        <div class="text-xs space-y-2.5">
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">Nama Pegawai:</span> <span class="font-bold text-slate-900 text-sm">{{ $cuti->pegawai->nama }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">NIP / NIK:</span> <span class="font-mono font-bold text-slate-900">{{ $cuti->pegawai->nip }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">Divisi / Prodi:</span> <span class="font-semibold text-blue-900 bg-blue-50 px-2 py-0.5 rounded">{{ $cuti->pegawai->divisi->nama_divisi ?? '-' }}</span></div>
                        </div>
                    </div>

                    <!-- Kolom Pengajuan -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 space-y-3">
                        <h4 class="font-display font-bold text-slate-900 border-b border-slate-200 pb-2 text-sm flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i>
                            <span>Informasi Izin / Cuti</span>
                        </h4>
                        <div class="text-xs space-y-2.5">
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">Jenis Cuti:</span> <span class="font-bold text-indigo-900">{{ $cuti->jenis_cuti }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">Tanggal Pelaksanaan:</span> <span class="font-bold text-slate-900">{{ $cuti->tanggal_mulai->translatedFormat('d M Y') }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">Durasi:</span> <span class="font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-lg">{{ $cuti->durasi_formatted ?? ($cuti->jumlah_hari . ' Hari') }}</span></div>
                            @if($cuti->alasan)
                                <div class="pt-1"><span class="text-slate-500 font-medium block mb-0.5">Keperluan:</span> <span class="font-medium italic text-slate-700">"{{ $cuti->alasan }}"</span></div>
                            @endif
                            @if($cuti->file_pendukung)
                                <div class="flex justify-between items-center pt-1 border-t border-slate-200"><span class="text-slate-500 font-medium">Lampiran:</span> <a href="{{ asset($cuti->file_pendukung) }}" target="_blank" class="text-blue-600 font-bold hover:underline flex items-center gap-1"><i data-lucide="paperclip" class="w-3.5 h-3.5"></i> Unduh Berkas</a></div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Button for Approved Leave -->
                @if($cuti->status === 'approved')
                    <div class="pt-6 border-t border-slate-200 flex justify-center">
                        <a href="{{ route('public.surat', $cuti->kode_tracking) }}" target="_blank"
                           class="px-8 py-4 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-500 text-white rounded-2xl font-display font-bold shadow-xl shadow-emerald-600/30 hover:shadow-2xl transition-all duration-200 flex items-center gap-3 text-sm">
                            <i data-lucide="printer" class="w-5 h-5"></i>
                            <span>Cetak Surat Izin Cuti Resmi (PDF / Print)</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @elseif($cutiList->isNotEmpty())
        <!-- Search List Results -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 p-6 sm:p-8 mb-8 text-slate-900">
            <h3 class="font-display font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                <i data-lucide="list-checks" class="w-5 h-5 text-blue-600"></i>
                <span>Hasil Riwayat Pengajuan Cuti</span>
            </h3>
            <div class="divide-y divide-slate-100">
                @foreach($cutiList as $item)
                    <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-slate-50 p-3 rounded-2xl transition-colors">
                        <div>
                            <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">{{ $item->kode_tracking }}</span>
                            <h4 class="font-display font-bold text-slate-900 text-sm mt-1.5">{{ $item->pegawai->nama }} &bull; <span class="text-blue-900 font-semibold">{{ $item->jenis_cuti }}</span></h4>
                            <p class="text-xs text-slate-500 mt-0.5">Tanggal: {{ $item->tanggal_mulai->format('d/m/Y') }} ({{ $item->durasi_formatted ?? ($item->jumlah_hari . ' Hari') }})</p>
                        </div>
                        <div>
                            <a href="{{ route('public.tracking', ['kode' => $item->kode_tracking]) }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow transition-all flex items-center gap-1.5">
                                <span>Detail Status</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($search)
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-10 text-center text-slate-900">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-3xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="search-x" class="w-8 h-8"></i>
            </div>
            <h3 class="font-display font-bold text-slate-900 text-lg">Pengajuan Cuti Tidak Ditemukan</h3>
            <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-md mx-auto">Tidak ada data cuti dengan Kode Tracking atau NIP "<strong>{{ $search }}</strong>". Mohon periksa kembali input Anda.</p>
        </div>
    @endif
</div>
@endsection
