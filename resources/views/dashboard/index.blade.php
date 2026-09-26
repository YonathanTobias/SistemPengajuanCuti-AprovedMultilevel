@extends('layouts.app')

@section('title', 'Dashboard Overview & Approval')

@section('content')
<div class="space-y-8">
    
    <!-- Header Welcome Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 p-6 sm:p-10 text-white shadow-2xl border border-slate-800/80">
        <!-- Ambient Mesh -->
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/15 text-blue-300 border border-blue-400/30 text-[11px] font-bold uppercase tracking-wider rounded-full mb-3 backdrop-blur-sm">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-blue-400"></i>
                    <span>Sistem Manajemen Cuti &amp; Persetujuan</span>
                </div>
                <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                    Selamat Datang, {{ $user->name }}
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    @if($user->isAdmin())
                        Dashboard Admin IT (Super Admin) &bull; Pengawasan Sistem, Master Data, dan Audit Institusi
                    @elseif($user->isKadiv())
                        Dashboard Persetujuan Cuti Level 1 &bull; Kepala Divisi / Kaprodi {{ $user->divisi ? '(' . $user->divisi->nama_divisi . ')' : '' }}
                    @elseif($user->isHrd())
                        Dashboard Persetujuan Cuti Level 2 &amp; Manajemen Data Kepegawaian (HRD)
                    @elseif($user->isKetua())
                        Dashboard Persetujuan Cuti Level 3 (Final) &bull; Ketua STIKes Panti Waluya Malang
                    @endif
                </p>
            </div>
            
            @if($user->canManageMaster())
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('pegawai.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-display font-bold shadow-lg shadow-blue-600/30 transition-all flex items-center gap-1.5">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>+ Tambah Pegawai</span>
                    </a>
                    <a href="{{ route('divisi.create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-display font-bold shadow-lg shadow-teal-600/30 transition-all flex items-center gap-1.5">
                        <i data-lucide="building" class="w-4 h-4"></i>
                        <span>+ Tambah Divisi</span>
                    </a>
                    <a href="{{ route('users.create') }}" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-display font-bold shadow-lg shadow-purple-600/30 transition-all flex items-center gap-1.5">
                        <i data-lucide="user-cog" class="w-4 h-4"></i>
                        <span>+ Akun User</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($user->canManageMaster())
        <!-- Feature Switch: Simpanan Jam Lembur -->
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-5 text-slate-900">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl {{ ($isLemburEnabled ?? false) ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 shadow-inner">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="font-display text-sm sm:text-base font-bold text-slate-900">Modul Simpanan Jam Lembur &amp; Cuti Kompensasi</span>
                        @if($isLemburEnabled ?? false)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase rounded-full border border-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                AKTIF
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 bg-slate-200 text-slate-700 text-[10px] font-extrabold uppercase rounded-full border border-slate-300">
                                NONAKTIF (UJI COBA CUTI MURNI)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        @if($isLemburEnabled ?? false)
                            Fitur klaim jam lembur dan penukaran cuti kompensasi sedang dibuka dan dapat diakses pegawai.
                        @else
                            Fitur lembur sedang dimatikan. Pegawai hanya dapat mengajukan permohonan Cuti biasa (Tahunan, Sakit, dll.).
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <form action="{{ route('settings.toggle-lembur') }}" method="POST">
                    @csrf
                    @if($isLemburEnabled ?? false)
                        <button type="submit" class="px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-2xl text-xs font-display font-bold transition-all flex items-center gap-2">
                            <i data-lucide="power" class="w-4 h-4"></i>
                            <span>Nonaktifkan Fitur Lembur</span>
                        </button>
                    @else
                        <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-display font-bold transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Aktifkan Fitur Lembur</span>
                        </button>
                    @endif
                </form>
            </div>
        </div>
    @endif

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 text-slate-900">
        @if($user->isAdmin())
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                    <i data-lucide="shield-check" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['total_users'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Akun Pejabat/User</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['total_pegawai'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Total Pegawai</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center shrink-0">
                    <i data-lucide="building-2" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['total_divisi'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Divisi / Prodi</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['total_cuti'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Permohonan ({{ $currentYear }})</div>
                </div>
            </div>
        @elseif($user->isKadiv())
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['pending'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Perlu Evaluasi Kadiv</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['approved'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Disetujui Sepenuhnya</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <i data-lucide="x-circle" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['rejected'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Pengajuan Ditolak</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['total_pegawai'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Pegawai Divisi Ini</div>
                </div>
            </div>
        @elseif($user->isHrd())
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['pending_hrd'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Perlu Approval HRD</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <i data-lucide="hourglass" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['pending_kadiv'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Menunggu Kadiv</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                    <i data-lucide="send" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['pending_ketua'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Menunggu Ketua</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['total_pegawai'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Total Pegawai</div>
                </div>
            </div>
        @else {{-- Ketua STIKes --}}
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                    <i data-lucide="file-check-2" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['pending_ketua'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Perlu Approval Ketua</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['approved'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Disetujui Sepenuhnya</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <i data-lucide="x-circle" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['rejected'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Pengajuan Ditolak</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-200">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="text-3xl font-black font-display text-slate-900">{{ $stats['total_pegawai'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Total Pegawai Aktif</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Filter & Table Section -->
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden text-slate-900">
        <div class="p-6 sm:p-8 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-display text-lg sm:text-xl font-bold text-slate-900">Daftar Pengajuan Cuti Pegawai</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola dan evaluasi persetujuan pengajuan cuti secara bertingkat</p>
            </div>

            <!-- Filter Status -->
            <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="rounded-2xl border-slate-300 text-xs font-bold text-slate-700 p-2.5 sm:p-3 bg-slate-50 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all cursor-pointer">
                    <option value="">-- Semua Status Pengajuan --</option>
                    <option value="pending_kadiv" {{ request('status') === 'pending_kadiv' ? 'selected' : '' }}>Pending Kadiv</option>
                    <option value="pending_hrd" {{ request('status') === 'pending_hrd' ? 'selected' : '' }}>Pending HRD</option>
                    <option value="pending_ketua" {{ request('status') === 'pending_ketua' ? 'selected' : '' }}>Pending Ketua</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                </select>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-display font-bold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-4">Kode / Tgl</th>
                        <th class="p-4">Pegawai / Divisi</th>
                        <th class="p-4">Jenis &amp; Durasi</th>
                        <th class="p-4">Alasan Cuti</th>
                        <th class="p-4">Status Approvals</th>
                        <th class="p-4 text-center">Aksi &amp; Keputusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($cutis as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Kode / Tgl -->
                            <td class="p-4">
                                <span class="font-mono font-bold text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">{{ $item->kode_tracking }}</span>
                                <div class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    <span>{{ $item->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>

                            <!-- Pegawai / Divisi -->
                            <td class="p-4">
                                <div class="font-display font-bold text-slate-900 text-sm">{{ $item->pegawai->nama }}</div>
                                <div class="text-slate-500 text-[11px] font-mono">NIP: {{ $item->pegawai->nip }}</div>
                                <div class="text-blue-800 font-semibold text-[11px] mt-0.5">{{ $item->pegawai->divisi->nama_divisi ?? '-' }}</div>
                            </td>

                            <!-- Jenis & Durasi -->
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $item->jenis_cuti }}</span>
                                <span class="text-emerald-800 font-bold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-lg inline-block mt-1">{{ $item->durasi_formatted ?? ($item->jumlah_hari . ' Hari') }}</span>
                                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span>{{ $item->tanggal_mulai->format('d/m/Y') }}</span>
                                    @if($item->tahun_cuti && $item->tahun_cuti != $item->tanggal_mulai->format('Y'))
                                        <span class="text-[10px] text-amber-800 font-bold bg-amber-100 px-1.5 py-0.5 rounded border border-amber-300" title="Kelonggaran cuti periode {{ $item->tahun_cuti }}">Kelonggaran {{ $item->tahun_cuti }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Alasan -->
                            <td class="p-4 max-w-xs">
                                <p class="line-clamp-2 text-slate-700 text-[11px] italic">"{{ $item->alasan ?: 'Cuti Tahunan Pegawai' }}"</p>
                                @if($item->file_pendukung)
                                    <a href="{{ asset($item->file_pendukung) }}" target="_blank" class="text-blue-600 font-bold text-[11px] hover:underline flex items-center gap-1 mt-1">
                                        <i data-lucide="paperclip" class="w-3 h-3"></i> Lampiran Berkas
                                    </a>
                                @endif
                            </td>

                            <!-- Status Approvals -->
                            <td class="p-4">
                                @if($item->status === 'pending_kadiv')
                                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1.5 border border-amber-300">
                                        <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                                        Menunggu Kadiv
                                    </span>
                                @elseif($item->status === 'pending_hrd')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1.5 border border-blue-300">
                                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                        Menunggu HRD
                                    </span>
                                @elseif($item->status === 'pending_ketua')
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1.5 border border-indigo-300">
                                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                                        Menunggu Ketua
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1 border border-emerald-300">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        Approved
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="px-3 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1 border border-rose-300">
                                        <i data-lucide="x" class="w-3.5 h-3.5 text-rose-600"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <div class="flex flex-col gap-1.5 items-center">
                                    <!-- Kadiv Action -->
                                    @if($user->isKadiv() && $item->status === 'pending_kadiv')
                                        <form action="{{ route('approval.kadiv', $item->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-display font-bold rounded-xl text-xs shadow transition-all flex items-center justify-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>Setujui (Kadiv)</span>
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->kode_tracking }}')" class="w-full px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl text-xs transition-colors flex items-center justify-center gap-1">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            <span>Tolak</span>
                                        </button>

                                    <!-- HRD Action -->
                                    @elseif($user->isHrd() && $item->status === 'pending_hrd')
                                        <form action="{{ route('approval.hrd', $item->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-display font-bold rounded-xl text-xs shadow transition-all flex items-center justify-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>Setujui (HRD)</span>
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->kode_tracking }}')" class="w-full px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl text-xs transition-colors flex items-center justify-center gap-1">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            <span>Tolak</span>
                                        </button>

                                    <!-- Ketua Action -->
                                    @elseif($user->isKetua() && $item->status === 'pending_ketua')
                                        <form action="{{ route('approval.ketua', $item->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-display font-bold rounded-xl text-xs shadow transition-all flex items-center justify-center gap-1">
                                                <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                                <span>Setujui (Final)</span>
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->kode_tracking }}')" class="w-full px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl text-xs transition-colors flex items-center justify-center gap-1">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            <span>Tolak</span>
                                        </button>

                                    @elseif($item->status === 'approved')
                                        <a href="{{ route('public.surat', $item->kode_tracking) }}" target="_blank" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs shadow flex items-center gap-1">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                            <span>Cetak Surat</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Selesai / Menunggu Tahap Lain</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-display font-bold text-sm text-slate-700">Belum Ada Data Pengajuan Cuti</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-slate-200">
            {{ $cutis->links() }}
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 border border-slate-200 text-slate-900 animate-in fade-in zoom-in-95 duration-200">
        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
        </div>
        <h3 class="font-display text-lg font-bold text-slate-900 mb-1">
            Tolak Pengajuan Cuti
        </h3>
        <p class="text-xs text-slate-500 mb-5">Kode Pengajuan: <span id="modalKodeTracking" class="font-mono font-bold text-blue-900"></span></p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-5">
                <label for="catatan_penolakan" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                    Alasan / Catatan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="catatan_penolakan" id="catatan_penolakan" rows="3" 
                          placeholder="Jelaskan alasan penolakan pengajuan cuti ini..." 
                          class="w-full rounded-2xl border-slate-300 p-3.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500" required></textarea>
            </div>

            <div class="flex justify-end gap-2.5">
                <button type="button" onclick="closeRejectModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold rounded-xl text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-display font-bold rounded-xl text-xs shadow-lg shadow-rose-600/30 transition-all">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openRejectModal(id, kode) {
        document.getElementById('modalKodeTracking').innerText = kode;
        document.getElementById('rejectForm').action = '/cuti/' + id + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }
</script>
@endpush
