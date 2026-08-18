@extends('layouts.app')

@section('title', "Arsip Cuti Pegawai - Tahun {$selectedYear}")

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/20 text-amber-300 rounded-full border border-amber-500/40 text-xs font-bold mb-3">
                <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                <span>Modul Arsip Tahunan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Arsip Cuti Pegawai Tahun {{ $selectedYear }}</h1>
            <p class="text-xs sm:text-sm text-blue-200 mt-1 max-w-2xl">
                Menampilkan seluruh riwayat pengajuan cuti pegawai yang telah diarsipkan untuk periode tahun <strong>{{ $selectedYear }}</strong>.
            </p>
        </div>

        <!-- Year Selector Dropdown -->
        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 shrink-0 w-full sm:w-auto">
            <form action="{{ route('arsip.index') }}" method="GET" class="space-y-2">
                <label for="tahun" class="block text-[11px] font-bold text-blue-200 uppercase tracking-wider">
                    Pilih Tahun Arsip:
                </label>
                <select name="tahun" id="tahun" onchange="this.form.submit()" 
                        class="w-full sm:w-48 rounded-xl border-white/30 bg-slate-900 text-white text-sm font-bold p-2.5 focus:ring-amber-400 focus:border-amber-400">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            📂 Tahun {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Summary Stats for Selected Archived Year -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengajuan {{ $selectedYear }}</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }} <span class="text-xs font-normal text-slate-500">Berkas</span></div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disetujui (Approved)</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['approved']) }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="text-xs font-bold text-rose-600 uppercase tracking-wider">Ditolak (Rejected)</div>
            <div class="text-2xl font-black text-rose-700 mt-1">{{ number_format($stats['rejected']) }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Proses / Pending</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ number_format($stats['pending']) }}</div>
        </div>
    </div>

    <!-- Filter & Export Card -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('arsip.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari Nama, NIP, Kode..." 
                       class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
            </div>

            <div>
                <select name="divisi_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
                    <option value="">-- Semua Divisi / Prodi --</option>
                    @foreach($divisis as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->nama_divisi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
                    <option value="">-- Semua Status --</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                    <option value="pending_kadiv" {{ request('status') == 'pending_kadiv' ? 'selected' : '' }}>Pending Kadiv</option>
                    <option value="pending_hrd" {{ request('status') == 'pending_hrd' ? 'selected' : '' }}>Pending HRD</option>
                    <option value="pending_ketua" {{ request('status') == 'pending_ketua' ? 'selected' : '' }}>Pending Ketua</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center gap-1.5">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Cari</span>
                </button>
                @if(request('search') || request('divisi_id') || request('status'))
                    <a href="{{ route('arsip.index', ['tahun' => $selectedYear]) }}" class="px-4 py-2.5 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                        Reset
                    </a>
                @endif

                @if(Auth::user()->isHrd())
                    <a href="{{ route('reports.export.xlsx', ['tgl_awal' => $selectedYear . '-01-01', 'tgl_akhir' => $selectedYear . '-12-31']) }}" 
                       class="px-3 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center gap-1 ml-auto" title="Export Excel Tahun {{ $selectedYear }}">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        <span>Excel</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Archive Table Card -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-4">Kode Tracking</th>
                        <th class="p-4">Pegawai</th>
                        <th class="p-4">Divisi / Prodi</th>
                        <th class="p-4">Jenis &amp; Tanggal Cuti</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Status Arsip</th>
                        <th class="p-4 text-center">Surat / Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($cutis as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $item->kode_tracking }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 text-sm block">{{ $item->pegawai->nama }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">NIP: {{ $item->pegawai->nip }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-blue-900 bg-blue-50 px-2.5 py-1 rounded border border-blue-200">{{ $item->pegawai->divisi->nama_divisi ?? '-' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $item->jenis_cuti }}</span>
                                <span class="text-[11px] text-slate-500 font-medium">Tanggal: {{ $item->tanggal_mulai->format('d/m/Y') }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 bg-slate-100 text-slate-800 font-bold rounded border border-slate-200">
                                    {{ $item->jumlah_hari }} Hari
                                </span>
                            </td>
                            <td class="p-4">
                                @if($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Approved
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ strtoupper($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($item->status === 'approved')
                                    <a href="{{ route('public.surat', $item->kode_tracking) }}" target="_blank" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg font-bold text-[11px] inline-flex items-center gap-1">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        <span>Cetak Surat</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Terarsip</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                <i data-lucide="archive" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-semibold">Belum Ada Data Arsip untuk Tahun {{ $selectedYear }}</p>
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
@endsection
