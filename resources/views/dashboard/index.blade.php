@extends('layouts.app')

@section('title', 'Dashboard Overview & Approval')

@section('content')
<div class="space-y-8">
    <!-- Header Welcome Card -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 text-white rounded-2xl p-6 sm:p-8 shadow-xl border border-indigo-900/40 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-400/30 text-xs font-bold uppercase tracking-wider rounded-full inline-block mb-2">
                    Portal Management
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Selamat Datang, {{ $user->name }}
                </h1>
                <p class="text-sm text-blue-200/80 mt-1">
                    @if($user->isKadiv())
                        Dashboard Persetujuan Cuti Level 1 - Kepala Divisi / Kaprodi {{ $user->divisi ? '(' . $user->divisi->nama_divisi . ')' : '' }}
                    @elseif($user->isHrd())
                        Dashboard Persetujuan Cuti Level 2 &amp; Manajemen Data Kepegawaian (HRD)
                    @elseif($user->isKetua())
                        Dashboard Persetujuan Cuti Final - Ketua STIKes Panti Waluya Malang
                    @endif
                </p>
            </div>
            
            @if($user->isHrd())
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('pegawai.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow transition-all flex items-center gap-1.5">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>+ Tambah Pegawai</span>
                    </a>
                    <a href="{{ route('divisi.create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold shadow transition-all flex items-center gap-1.5">
                        <i data-lucide="building" class="w-4 h-4"></i>
                        <span>+ Tambah Divisi/Prodi</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @if($user->isKadiv())
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['pending'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Perlu Persetujuan Kadiv</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['approved'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cuti Disetujui Sepenuhnya</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['rejected'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cuti Ditolak</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['total_pegawai'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pegawai Divisi Ini</div>
                </div>
            </div>
        @elseif($user->isHrd())
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['pending_hrd'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Perlu Persetujuan HRD</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <i data-lucide="hourglass" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['pending_kadiv'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Kadiv</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                    <i data-lucide="send" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['pending_ketua'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Ketua</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['total_pegawai'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pegawai</div>
                </div>
            </div>
        @else {{-- Ketua STIKes --}}
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['pending_ketua'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Persetujuan Anda</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['approved'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disetujui Sepenuhnya</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['rejected'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengajuan Ditolak</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['total_pegawai'] }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pegawai</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Filter & Table Section -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Pengajuan Cuti Pegawai</h2>
                <p class="text-xs text-slate-500">Kelola dan evaluasi pengajuan cuti secara bertingkat</p>
            </div>

            <!-- Filter Status -->
            <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-300 text-xs font-semibold text-slate-700 p-2 bg-slate-50 focus:ring-blue-500">
                    <option value="">-- Semua Status --</option>
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
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider">
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
                        <tr class="hover:bg-slate-50 transition-colors">
                            <!-- Kode / Tgl -->
                            <td class="p-4">
                                <span class="font-mono font-bold text-blue-900 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $item->kode_tracking }}</span>
                                <div class="text-[11px] text-slate-500 mt-1">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                            </td>

                            <!-- Pegawai / Divisi -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $item->pegawai->nama }}</div>
                                <div class="text-slate-500">NIP: <span class="font-mono">{{ $item->pegawai->nip }}</span></div>
                                <div class="text-blue-700 font-semibold text-[11px]">{{ $item->pegawai->divisi->nama_divisi ?? '-' }}</div>
                            </td>

                            <!-- Jenis & Durasi -->
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $item->jenis_cuti }}</span>
                                <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded inline-block mt-1">{{ $item->jumlah_hari }} Hari</span>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }}</div>
                            </td>

                            <!-- Alasan -->
                            <td class="p-4 max-w-xs">
                                <p class="line-clamp-2 text-slate-700 text-[11px] italic">"{{ $item->alasan }}"</p>
                                @if($item->file_pendukung)
                                    <a href="{{ asset($item->file_pendukung) }}" target="_blank" class="text-blue-600 font-bold text-[11px] hover:underline flex items-center gap-1 mt-1">
                                        <i data-lucide="paperclip" class="w-3 h-3"></i> Lampiran Berkas
                                    </a>
                                @endif
                            </td>

                            <!-- Status Approvals -->
                            <td class="p-4">
                                @if($item->status === 'pending_kadiv')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                        Menunggu Approval Kadiv
                                    </span>
                                @elseif($item->status === 'pending_hrd')
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                        Menunggu Approval HRD
                                    </span>
                                @elseif($item->status === 'pending_ketua')
                                    <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                                        Menunggu Approval Ketua
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                        Approved (Disetujui)
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-[11px] inline-flex items-center gap-1">
                                        <i data-lucide="x" class="w-3 h-3 text-rose-600"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <div class="flex flex-col gap-1 items-center">
                                    <!-- Kadiv Action -->
                                    @if($user->isKadiv() && $item->status === 'pending_kadiv')
                                        <form action="{{ route('approval.kadiv', $item->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow transition-colors flex items-center justify-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>Setujui (Kadiv)</span>
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->kode_tracking }}')" class="w-full px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs shadow transition-colors flex items-center justify-center gap-1">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            <span>Tolak</span>
                                        </button>

                                    <!-- HRD Action -->
                                    @elseif($user->isHrd() && $item->status === 'pending_hrd')
                                        <form action="{{ route('approval.hrd', $item->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow transition-colors flex items-center justify-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>Setujui (HRD)</span>
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->kode_tracking }}')" class="w-full px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs shadow transition-colors flex items-center justify-center gap-1">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            <span>Tolak</span>
                                        </button>

                                    <!-- Ketua Action -->
                                    @elseif($user->isKetua() && $item->status === 'pending_ketua')
                                        <form action="{{ route('approval.ketua', $item->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-xs shadow transition-colors flex items-center justify-center gap-1">
                                                <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                                <span>Setujui (Final)</span>
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->kode_tracking }}')" class="w-full px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs shadow transition-colors flex items-center justify-center gap-1">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            <span>Tolak</span>
                                        </button>

                                    @elseif($item->status === 'approved')
                                        <a href="{{ route('public.surat', $item->kode_tracking) }}" target="_blank" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg text-xs shadow flex items-center gap-1">
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
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-semibold">Belum Ada Data Pengajuan Cuti</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $cutis->links() }}
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200">
        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-2">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
            Tolak Pengajuan Cuti
        </h3>
        <p class="text-xs text-slate-500 mb-4">Pengajuan Kode: <span id="modalKodeTracking" class="font-mono font-bold text-slate-800"></span></p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label for="catatan_penolakan" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Alasan / Catatan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="catatan_penolakan" id="catatan_penolakan" rows="3" 
                          placeholder="Jelaskan alasan penolakan pengajuan cuti ini..." 
                          class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-rose-500 focus:border-rose-500" required></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow">
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
