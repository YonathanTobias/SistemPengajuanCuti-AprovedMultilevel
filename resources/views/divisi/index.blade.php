@extends('layouts.app')

@section('title', 'Kelola Divisi / Prodi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Divisi / Program Studi</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar Divisi/Prodi &amp; Akun Kepala Divisi (Kadiv/Kaprodi) Otomatis</p>
        </div>

        <a href="{{ route('divisi.create') }}" class="px-5 py-3 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white rounded-xl font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>+ Tambah Divisi / Prodi Baru</span>
        </a>
    </div>

    <!-- Alert Note -->
    <div class="p-4 bg-teal-50 border border-teal-200 rounded-xl text-teal-900 text-xs flex items-start gap-3">
        <i data-lucide="sparkles" class="w-5 h-5 text-teal-600 shrink-0 mt-0.5"></i>
        <div>
            <span class="font-bold block text-sm mb-0.5">Fitur Otomatisasi Akun Kepala Divisi:</span>
            Setiap kali Anda menekan tombol <strong>Tambah Divisi</strong>, sistem secara otomatis akan membuat akun login untuk <strong>Kepala Divisi / Kaprodi</strong> dari divisi tersebut sehingga siap digunakan untuk persetujuan cuti Level 1!
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-4">Kode Divisi</th>
                        <th class="p-4">Nama Divisi / Prodi</th>
                        <th class="p-4">Akun Kepala Divisi (Kadiv) Otomatis</th>
                        <th class="p-4">Jumlah Pegawai</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($divisis as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded border border-teal-200">{{ $item->kode_divisi }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 text-sm block">{{ $item->nama_divisi }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $item->deskripsi ?: 'Tidak ada deskripsi' }}</span>
                            </td>
                            <td class="p-4">
                                @if($item->kadivUser)
                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200 space-y-1">
                                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                            <span>{{ $item->kadivUser->name }}</span>
                                        </div>
                                        <div class="font-mono text-[11px] text-blue-700">{{ $item->kadivUser->email }}</div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Belum ada akun Kadiv</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 bg-slate-100 text-slate-800 font-bold rounded-full text-xs">
                                    {{ $item->pegawais->count() }} Pegawai
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('divisi.edit', $item->id) }}" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg font-semibold text-xs border border-amber-200 transition-colors" title="Edit Divisi">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('divisi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi {{ $item->nama_divisi }}? Akun Kadiv terkait juga akan dihapus!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold text-xs border border-rose-200 transition-colors" title="Hapus Divisi">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                <i data-lucide="building" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-semibold">Belum Ada Data Divisi/Prodi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
