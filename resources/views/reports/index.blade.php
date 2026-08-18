@extends('layouts.app')

@section('title', 'Laporan & Export Data Cuti')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Laporan &amp; Export Rekapitulasi Cuti</h1>
            <p class="text-xs text-slate-500 mt-1">Unduh dan cetak rekapitulasi pengajuan cuti pegawai dalam format Excel (.XLSX), CSV, atau PDF</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Export XLSX -->
            <a href="{{ route('reports.export.xlsx', request()->query()) }}" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-bold text-xs shadow transition-all flex items-center gap-1.5">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Download .XLSX</span>
            </a>

            <!-- Export CSV -->
            <a href="{{ route('reports.export.csv', request()->query()) }}" class="px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-xl font-bold text-xs shadow transition-all flex items-center gap-1.5">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Download .CSV</span>
            </a>

            <!-- Export PDF / Print -->
            <a href="{{ route('reports.export.pdf', request()->query()) }}" target="_blank" class="px-4 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl font-bold text-xs shadow transition-all flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('reports.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                <!-- Filter Tahun -->
                <div>
                    <label for="tahun" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Filter Tahun
                    </label>
                    <select name="tahun" id="tahun" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-bold focus:ring-blue-500">
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
                    <label for="divisi_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Divisi / Prodi
                    </label>
                    <select name="divisi_id" id="divisi_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
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
                    <label for="pegawai_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Pegawai Specific
                    </label>
                    <select name="pegawai_id" id="pegawai_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
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
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Status Cuti
                    </label>
                    <select name="status" id="status" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
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
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Rentang Tanggal
                    </label>
                    <div class="flex items-center gap-1">
                        <input type="date" name="tgl_awal" value="{{ request('tgl_awal') }}" class="w-full rounded-xl border-slate-300 p-2 bg-slate-50 text-[11px]">
                        <span class="text-slate-400 font-bold">-</span>
                        <input type="date" name="tgl_akhir" value="{{ request('tgl_akhir') }}" class="w-full rounded-xl border-slate-300 p-2 bg-slate-50 text-[11px]">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan Filter</span>
                </button>
                @if(request('tahun') || request('divisi_id') || request('pegawai_id') || request('status') || request('tgl_awal') || request('tgl_akhir'))
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Summary & Table -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700">
                Menampilkan <strong>{{ $cutis->count() }}</strong> Data Pengajuan Cuti
                @if(request('tahun'))
                    <span class="text-blue-700 ml-1 bg-blue-100 px-2 py-0.5 rounded border border-blue-200">Tahun {{ request('tahun') }}</span>
                @endif
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider">
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
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-slate-500 font-bold">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $item->kode_tracking }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $item->pegawai->nama }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">NIP: {{ $item->pegawai->nip }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-blue-900 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $item->pegawai->divisi->nama_divisi ?? '-' }}</span>
                            </td>
                            <td class="p-4 font-bold text-slate-800">{{ $item->jenis_cuti }}</td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900">{{ $item->tanggal_mulai->format('d/m/Y') }}</span>
                            </td>
                            <td class="p-4">
                                @if($item->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Approved
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        {{ strtoupper($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs truncate italic">
                                "{{ $item->alasan }}"
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('reports.export.pegawai.xlsx', $item->pegawai_id) }}" class="px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded font-bold text-[10px]" title="Export Cuti Pegawai Ini ke Excel">
                                        .XLSX
                                    </a>
                                    <a href="{{ route('reports.export.pegawai.csv', $item->pegawai_id) }}" class="px-2 py-1 bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 rounded font-bold text-[10px]" title="Export Cuti Pegawai Ini ke CSV">
                                        .CSV
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500">
                                <i data-lucide="file-spreadsheet" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-semibold">Tidak Ada Data Cuti Sesuai Filter</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
