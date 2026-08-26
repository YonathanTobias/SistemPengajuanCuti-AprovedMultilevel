@extends('layouts.app')

@section('title', 'Manajemen Persetujuan Jam Lembur')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Persetujuan Klaim Jam Lembur</h1>
            <p class="text-xs text-slate-500 mt-1">Verifikasi pengajuan jam lembur pegawai untuk dimasukkan ke Saldo Simpanan Lembur</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('public.pengajuan_lembur') }}" target="_blank" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Form Klaim Lembur Publik</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('lembur.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="w-48">
                <select name="status" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-300 p-2.5 bg-slate-50 focus:ring-amber-500">
                    <option value="">-- Semua Status --</option>
                    <option value="pending_kadiv" {{ request('status') == 'pending_kadiv' ? 'selected' : '' }}>Menunggu Kadiv</option>
                    <option value="pending_hrd" {{ request('status') == 'pending_hrd' ? 'selected' : '' }}>Menunggu HRD</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            @if(request()->has('status'))
                <a href="{{ route('lembur.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset Filter</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kode &amp; Tanggal</th>
                        <th class="p-4">Pegawai &amp; Divisi</th>
                        <th class="p-4 text-center">Durasi Lembur</th>
                        <th class="p-4">Kegiatan / Keperluan</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Aksi / Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($lemburs as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-bold text-amber-900 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 block w-fit mb-1">{{ $item->kode_tracking }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $item->tanggal_lembur->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $item->pegawai->nama }}</div>
                                <div class="text-[11px] text-slate-500">{{ $item->pegawai->nip }} &bull; {{ $item->pegawai->divisi->nama_divisi ?? '-' }}</div>
                                <div class="text-[11px] text-amber-700 font-bold mt-0.5">Saldo Saat Ini: {{ $item->pegawai->saldo_lembur_formatted }}</div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 font-bold rounded-lg text-xs">
                                    +{{ $item->durasi_formatted }}
                                </span>
                            </td>
                            <td class="p-4 max-w-xs">
                                <div class="truncate">{{ $item->kegiatan }}</div>
                                @if($item->file_bukti)
                                    <a href="{{ asset('uploads/berkas/' . $item->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline text-[11px] flex items-center gap-1 mt-1 font-semibold">
                                        <i data-lucide="paperclip" class="w-3 h-3"></i>
                                        <span>Lihat Lampiran Bukti</span>
                                    </a>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($item->status === 'pending_kadiv')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px]">Menunggu Kadiv</span>
                                @elseif($item->status === 'pending_hrd')
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-[10px]">Menunggu HRD</span>
                                @elseif($item->status === 'approved')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">Disetujui (+{{ $item->durasi_formatted }})</span>
                                @elseif($item->status === 'rejected')
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-[10px]">Ditolak</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    {{-- Kadiv Approval Action --}}
                                    @if(Auth::user()->isKadiv() && $item->status === 'pending_kadiv')
                                        <form action="{{ route('lembur.approve-kadiv', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold text-[11px] shadow-sm">
                                                Setujui Kadiv &rarr;
                                            </button>
                                        </form>
                                    @endif

                                    {{-- HRD Approval Action (Final) --}}
                                    @if(Auth::user()->isHrd() && in_array($item->status, ['pending_kadiv', 'pending_hrd']))
                                        <form action="{{ route('lembur.approve-hrd', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[11px] shadow-sm">
                                                Setujui HRD (Final)
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reject Action Button --}}
                                    @if(in_array($item->status, ['pending_kadiv', 'pending_hrd']))
                                        <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->kode_tracking }}')" class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-lg font-bold text-[11px]">
                                            Tolak
                                        </button>
                                    @endif

                                    @if($item->status === 'approved')
                                        <span class="text-emerald-600 font-bold text-[11px]">✓ Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                Belum ada data pengajuan klaim jam lembur.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lemburs->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $lemburs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tolak Lembur -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Tolak Pengajuan Lembur</h3>
        <p class="text-xs text-slate-600" id="rejectModalSub"></p>
        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="catatan_penolakan" class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="catatan_penolakan" id="catatan_penolakan" rows="3" required class="w-full text-xs rounded-xl border-slate-300 p-2.5 bg-slate-50" placeholder="Tuliskan alasan penolakan..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openRejectModal(id, code) {
        document.getElementById('rejectModalSub').innerText = 'Kode Tracking: ' + code;
        document.getElementById('rejectForm').action = '/lembur/' + id + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endpush
