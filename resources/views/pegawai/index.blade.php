@extends('layouts.app')

@section('title', 'Kelola Data Pegawai')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Data Pegawai</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar Pegawai STIKes Panti Waluya Malang &amp; Sisa Kuota Cuti Tahunan</p>
        </div>

        <a href="{{ route('pegawai.create') }}" class="px-5 py-3 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>+ Tambah Pegawai Baru</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('pegawai.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari NIP, Nama, atau Jabatan..." 
                       class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <select name="divisi_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Divisi / Prodi --</option>
                    @foreach($divisis as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->nama_divisi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center gap-1.5">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Cari</span>
                </button>
                @if(request('search') || request('divisi_id'))
                    <a href="{{ route('pegawai.index') }}" class="px-4 py-2.5 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-4">NIP</th>
                        <th class="p-4">Nama Pegawai</th>
                        <th class="p-4">Divisi / Prodi</th>
                        <th class="p-4">Jabatan</th>
                        <th class="p-4">Kontak (Email / HP)</th>
                        <th class="p-4">Sisa / Total Cuti</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pegawais as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $item->nip }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 text-sm block">{{ $item->nama }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-blue-900 bg-blue-50 px-2.5 py-1 rounded border border-blue-200">{{ $item->divisi->nama_divisi ?? '-' }}</span>
                            </td>
                            <td class="p-4 text-slate-700">
                                {{ $item->jabatan }}
                            </td>
                            <td class="p-4 text-[11px]">
                                <div class="font-mono text-slate-800">{{ $item->email }}</div>
                                <div class="text-slate-500">{{ $item->no_hp }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 font-bold rounded-lg border border-emerald-200 text-xs">
                                    {{ $item->sisa_cuti }} / {{ $item->jatah_cuti }} Hari
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pegawai.edit', $item->id) }}" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg font-semibold text-xs border border-amber-200 transition-colors" title="Edit Pegawai">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('pegawai.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $item->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold text-xs border border-rose-200 transition-colors" title="Hapus Pegawai">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                <i data-lucide="users" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-semibold">Belum Ada Data Pegawai</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $pegawais->links() }}
        </div>
    </div>
</div>
@endsection
