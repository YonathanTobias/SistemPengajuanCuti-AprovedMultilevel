@extends('layouts.app')

@section('title', 'Laporan & Export Data Cuti')

@section('content')
<div class="space-y-8 text-slate-900">
    
    <!-- Header Banner & Action Buttons -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 p-6 sm:p-10 text-white shadow-2xl border border-slate-800/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-teal-500/15 text-teal-300 rounded-full border border-teal-500/30 text-[11px] font-bold uppercase tracking-wider mb-3 backdrop-blur-sm">
                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-teal-400"></i>
                <span>Laporan &amp; Ekspor Data Resmi</span>
            </div>
            <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                Rekapitulasi &amp; Ekspor Cuti Pegawai
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                Unduh dan cetak rekapitulasi data pengajuan cuti pegawai STIKes Panti Waluya Malang dalam format Excel (.XLSX), CSV, atau PDF Resmi.
            </p>
        </div>

        <!-- Export Action Toolbar -->
        <div class="flex items-center gap-2.5 flex-wrap shrink-0 relative z-10">
            <!-- Export XLSX -->
            <a href="{{ route('reports.export.xlsx', request()->query()) }}" class="px-4 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-display font-bold text-xs shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Download .XLSX</span>
            </a>

            <!-- Export CSV -->
            <a href="{{ route('reports.export.csv', request()->query()) }}" class="px-4 py-3 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl font-display font-bold text-xs shadow-lg shadow-teal-600/30 transition-all flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Download .CSV</span>
            </a>

            <!-- Export PDF / Print -->
            <a href="{{ route('reports.export.pdf', request()->query()) }}" target="_blank" class="px-4 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-display font-bold text-xs shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90">
        <form action="{{ route('reports.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Filter Tahun -->
                <div>
                    <label for="tahun" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Periode Tahun
                    </label>
                    <select name="tahun" id="tahun" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 cursor-pointer">
                        <option value="">-- Semua Tahun --</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                Tahun {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Divisi -->
                <div>
                    <label for="divisi_id" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Divisi / Prodi
                    </label>
                    <select name="divisi_id" id="divisi_id" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 cursor-pointer">
                        <option value="">-- Semua Divisi --</option>
                        @foreach($divisis as $div)
                            <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Pegawai -->
                <div>
                    <label for="pegawai_id" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pegawai Tertentu
                    </label>
                    <select name="pegawai_id" id="pegawai_id" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 cursor-pointer">
                        <option value="">-- Semua Pegawai --</option>
                        @foreach($pegawais as $p)
                            <option value="{{ $p->id }}" {{ request('pegawai_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} ({{ $p->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label for="status" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Persetujuan
                    </label>
                    <select name="status" id="status" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 cursor-pointer">
                        <option value="">-- Semua Status --</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                        <option value="pending_kadiv" {{ request('status') == 'pending_kadiv' ? 'selected' : '' }}>Pending Kadiv</option>
                        <option value="pending_hrd" {{ request('status') == 'pending_hrd' ? 'selected' : '' }}>Pending HRD</option>
                        <option value="pending_ketua" {{ request('status') == 'pending_ketua' ? 'selected' : '' }}>Pending Ketua</option>
                    </select>
                </div>

                <!-- Rentang Tanggal Spesifik -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Rentang Tanggal
                    </label>
                    <div class="flex items-center gap-1.5">
                        <input type="date" name="tgl_awal" value="{{ request('tgl_awal') }}" class="w-full rounded-2xl border-slate-300 p-2.5 bg-slate-50 text-xs font-semibold">
                        <span class="text-slate-400 font-bold">-</span>
                        <input type="date" name="tgl_akhir" value="{{ request('tgl_akhir') }}" class="w-full rounded-2xl border-slate-300 p-2.5 bg-slate-50 text-xs font-semibold">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-display font-bold rounded-2xl text-xs flex items-center gap-1.5 shadow transition-all">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan Filter</span>
                </button>
                @if(request('tahun') || request('divisi_id') || request('pegawai_id') || request('status') || request('tgl_awal') || request('tgl_akhir'))
                    <a href="{{ route('reports.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold rounded-2xl text-xs transition-colors">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Summary & Table -->
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden">
        <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700">
                Menampilkan <strong>{{ $cutis->count() }}</strong> Berkas Cuti
                @if(request('tahun'))
                    <span class="text-blue-700 ml-1.5 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">Tahun {{ request('tahun') }}</span>
                @endif
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-display font-bold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-4">No</th>
                        <th class="p-4">Kode Tracking</th>
                        <th class="p-4">Pegawai</th>
                        <th class="p-4">Divisi / Prodi</th>
                        <th class="p-4">Jenis Cuti</th>
                        <th class="p-4">Tgl Cuti</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Alasan Cuti</th>
                        <th class="p-4 text-center">Export Pegawai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($cutis as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 text-slate-500 font-bold">{{ $index + 1 }}</td>
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
                            <td class="p-4 font-bold text-slate-800">{{ $item->jenis_cuti }}</td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900">{{ $item->tanggal_mulai->format('d/m/Y') }}</span>
                                @if($item->tahun_cuti && $item->tahun_cuti != $item->tanggal_mulai->format('Y'))
                                    <div class="mt-1">
                                        <span class="text-[10px] text-amber-800 font-bold bg-amber-100 px-2 py-0.5 rounded-md border border-amber-300" title="Kelonggaran cuti periode {{ $item->tahun_cuti }}">Kelonggaran {{ $item->tahun_cuti }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($item->status === 'approved')
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 inline-block">
                                        Approved
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300 inline-block">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300 inline-block">
                                        {{ strtoupper($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 max-w-xs">
                                <span class="text-slate-600 line-clamp-1 italic text-[11px]">"{{ $item->alasan ?: 'Cuti Tahunan Pegawai' }}"</span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('reports.export.pegawai.xlsx', $item->pegawai_id) }}" class="p-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl font-bold text-[11px] inline-flex items-center gap-1 transition-colors" title="Export Excel Individu">
                                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                                        <span>XLSX</span>
                                    </a>
                                    <a href="{{ route('reports.export.pegawai.csv', $item->pegawai_id) }}" class="p-2 bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-xl font-bold text-[11px] inline-flex items-center gap-1 transition-colors" title="Export CSV Individu">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        <span>CSV</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-10 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-display font-bold text-sm text-slate-700">Tidak Ada Data Cuti yang Sesuai Filter</p>
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
