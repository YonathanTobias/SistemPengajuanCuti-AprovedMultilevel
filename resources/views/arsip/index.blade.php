@extends('layouts.app')

@section('title', "Arsip Cuti Pegawai - Tahun {$selectedYear}")

@section('content')
<div class="space-y-8 text-slate-900">
    
    <!-- Header Banner & Year Selector -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 p-6 sm:p-10 text-white shadow-2xl border border-slate-800/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/15 text-amber-300 rounded-full border border-amber-500/30 text-[11px] font-bold uppercase tracking-wider mb-3 backdrop-blur-sm">
                <i data-lucide="archive" class="w-3.5 h-3.5 text-amber-400"></i>
                <span>Bank Data &amp; Arsip Tahunan</span>
            </div>
            <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                Arsip Cuti Pegawai Periode {{ $selectedYear }}
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                Menampilkan seluruh riwayat cuti pegawai yang telah tercatat dan diarsipkan untuk periode tahun <strong class="text-white">{{ $selectedYear }}</strong>.
            </p>
        </div>

        <!-- Year Selector Dropdown -->
        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 shrink-0 w-full sm:w-auto relative z-10">
            <form action="{{ route('arsip.index') }}" method="GET" class="space-y-1.5">
                <label for="tahun" class="block text-[11px] font-bold text-blue-200 uppercase tracking-wider">
                    Pilih Periode Tahun Arsip:
                </label>
                <select name="tahun" id="tahun" onchange="this.form.submit()" 
                        class="w-full sm:w-52 rounded-xl border-white/30 bg-slate-900 text-white text-xs sm:text-sm font-display font-bold p-3 focus:ring-4 focus:ring-amber-400/20 focus:border-amber-400 cursor-pointer">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            📂 Periode Tahun {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Summary Stats for Selected Archived Year -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
        <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex flex-col justify-between">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Pengajuan {{ $selectedYear }}</div>
            <div class="text-3xl font-black font-display text-slate-900 mt-2">{{ number_format($totalPengajuan) }} <span class="text-xs font-semibold text-slate-400">Berkas</span></div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex flex-col justify-between">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Disetujui (Approved)</div>
            <div class="text-3xl font-black font-display text-emerald-700 mt-2">{{ number_format($totalApproved) }}</div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex flex-col justify-between">
            <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Ditolak (Rejected)</div>
            <div class="text-3xl font-black font-display text-rose-700 mt-2">{{ number_format($totalRejected) }}</div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90 flex flex-col justify-between">
            <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Total Durasi Diambil</div>
            <div class="text-3xl font-black font-display text-blue-700 mt-2">{{ number_format($totalHariCuti) }} <span class="text-xs font-semibold text-slate-400">Hari</span></div>
        </div>
    </div>

    <!-- Filter & Export Card -->
    <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90">
        <form action="{{ route('arsip.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari Nama, NIP, Kode Tracking..." 
                       class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600">
            </div>

            <div>
                <select name="divisi_id" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 cursor-pointer">
                    <option value="">-- Semua Divisi / Prodi --</option>
                    @foreach($divisis as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->nama_divisi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 cursor-pointer">
                    <option value="">-- Semua Status --</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                    <option value="pending_kadiv" {{ request('status') == 'pending_kadiv' ? 'selected' : '' }}>Pending Kadiv</option>
                    <option value="pending_hrd" {{ request('status') == 'pending_hrd' ? 'selected' : '' }}>Pending HRD</option>
                    <option value="pending_ketua" {{ request('status') == 'pending_ketua' ? 'selected' : '' }}>Pending Ketua</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white font-display font-bold rounded-2xl text-xs flex items-center gap-1.5 shadow transition-all">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari</span>
                </button>
                @if(request('search') || request('divisi_id') || request('status'))
                    <a href="{{ route('arsip.index', ['tahun' => $selectedYear]) }}" class="px-4 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold rounded-2xl text-xs transition-colors">
                        Reset
                    </a>
                @endif

                @if(Auth::user()->isHrd())
                    <a href="{{ route('reports.export.xlsx', ['tgl_awal' => $selectedYear . '-01-01', 'tgl_akhir' => $selectedYear . '-12-31']) }}" 
                       class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-display font-bold rounded-2xl text-xs flex items-center gap-1.5 ml-auto shadow-lg shadow-emerald-600/20 transition-all" title="Export Excel Tahun {{ $selectedYear }}">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        <span>Excel</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Archive Table Card -->
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-display font-bold border-b border-slate-200 uppercase tracking-wider">
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
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">{{ $item->kode_tracking }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-display font-bold text-slate-900 text-sm block">{{ $item->pegawai->nama }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">NIP: {{ $item->pegawai->nip }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">{{ $item->pegawai->divisi->nama_divisi ?? '-' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $item->jenis_cuti }}</span>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                    <span class="text-[11px] text-slate-500 font-medium">Tanggal: {{ $item->tanggal_mulai->format('d/m/Y') }}</span>
                                    @if($item->tahun_cuti && $item->tahun_cuti != $item->tanggal_mulai->format('Y'))
                                        <span class="text-[10px] text-amber-800 font-bold bg-amber-100 px-2 py-0.5 rounded-md border border-amber-300" title="Kelonggaran cuti periode {{ $item->tahun_cuti }}">Kelonggaran {{ $item->tahun_cuti }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-800 font-bold rounded-lg border border-slate-200">
                                    {{ $item->durasi_formatted ?? ($item->jumlah_hari . ' Hari') }}
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
                                    <a href="{{ route('public.surat', $item->kode_tracking) }}" target="_blank" class="px-3.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-xl font-bold text-[11px] inline-flex items-center gap-1.5 shadow-sm transition-colors">
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
                            <td colspan="7" class="p-10 text-center text-slate-500">
                                <i data-lucide="archive" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-display font-bold text-sm text-slate-700">Belum Ada Data Arsip untuk Tahun {{ $selectedYear }}</p>
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
@endsection
