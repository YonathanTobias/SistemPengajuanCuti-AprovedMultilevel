@extends('layouts.app')

@section('title', 'Kelola Data Pegawai & Kuota Cuti')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Data Pegawai &amp; Kuota Cuti</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data pegawai, edit kuota cuti tahunan, dan reset kuota otomatis setiap 1 Januari</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Manual Reset Button -->
            <button type="button" onclick="openResetModal()" class="px-4 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs shadow-md transition-all flex items-center gap-1.5" title="Reset Kuota Seluruh Pegawai">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                <span>Reset Kuota (1 Jan)</span>
            </button>

            <!-- Add Pegawai -->
            <a href="{{ route('pegawai.create') }}" class="px-5 py-3 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>+ Tambah Pegawai Baru</span>
            </a>
        </div>
    </div>

    <!-- Info Reset Otomatis Badge -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-900 flex items-start gap-3">
        <i data-lucide="calendar" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
        <div>
            <strong class="font-bold text-sm block mb-0.5">Pengaturan Scheduler Reset Kuota Otomatis (1 Januari):</strong>
            Sistem secara otomatis telah dijadwalkan via Artisan Command (<code class="bg-blue-100 px-1 py-0.5 rounded font-mono text-blue-800">cuti:reset-kuota</code>) untuk mereset sisa kuota cuti seluruh pegawai menjadi <strong>0 hari</strong> tepat pada setiap tanggal <strong>1 Januari pukul 00:00 WIB</strong>. HRD juga dapat memicu reset manual kapan saja melalui tombol di atas.
        </div>
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
                        <th class="p-4">Kontak</th>
                        <th class="p-4">Sisa / Total Kuota Cuti</th>
                        <th class="p-4 text-center">Aksi Kuota &amp; Data</th>
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
                                <div class="space-y-1">
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 font-bold rounded-lg border border-emerald-200 text-xs inline-block">
                                        Sisa: {{ $item->sisa_cuti }} Hari
                                    </span>
                                    <div class="text-[11px] text-slate-500">Total Kuota: {{ $item->jatah_cuti }} Hari/Tahun</div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <!-- Tombol Tambah Cuti Khusus Kantor -->
                                    <button type="button" onclick="openCutiKhususModal({{ $item->id }}, '{{ $item->nama }}')" 
                                            class="px-2.5 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg text-xs shadow transition-colors flex items-center gap-1"
                                            title="Tambah Cuti Khusus dari Kantor">
                                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                        <span>+ Cuti Khusus</span>
                                    </button>

                                    <!-- Edit Pegawai & Kuota -->
                                    <a href="{{ route('pegawai.edit', $item->id) }}" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg font-semibold text-xs border border-amber-200 transition-colors" title="Edit Data & Kuota Cuti">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('pegawai.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $item->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold text-xs border border-rose-200 transition-colors" title="Hapus Pegawai">
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

<!-- Modal Reset Kuota Manual (1 Jan) -->
<div id="resetModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200">
        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-2 text-amber-700">
            <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
            Reset Sisa Kuota Cuti Seluruh Pegawai
        </h3>
        <p class="text-xs text-slate-600 mb-4">
            Apakah Anda yakin ingin mereset sisa kuota cuti untuk <strong>seluruh pegawai STIKes</strong>? Tindakan ini akan mengubah kuota sisa cuti menjadi nilai target (misal: 0 hari untuk reset pergantian tahun).
        </p>

        <form action="{{ route('pegawai.reset-kuota-manual') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label for="target_quota" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Set Sisa Kuota Cuti Menjadi <span class="text-rose-500">*</span>
                </label>
                <select name="target_quota" id="target_quota" class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-amber-500">
                    <option value="0" selected>0 Hari (Reset Akhir Tahun 1 Jan)</option>
                    <option value="12">12 Hari (Reset Kuota Baru Tahun Baru)</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeResetModal()" class="px-4 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs shadow">
                    Konfirmasi Reset Kuota
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Cuti Khusus Kantor -->
<div id="cutiKhususModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200">
        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-1">
            <i data-lucide="gift" class="w-5 h-5 text-teal-600"></i>
            Tambah Cuti Khusus Kantor
        </h3>
        <p class="text-xs text-slate-500 mb-4">Pegawai: <span id="modalNamaPegawai" class="font-bold text-slate-900"></span></p>

        <form id="cutiKhususForm" method="POST" action="">
            @csrf
            <div class="space-y-4 mb-5">
                <div>
                    <label for="jumlah_tambahan" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Jumlah Tambahan Hari Cuti <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="jumlah_tambahan" id="jumlah_tambahan" 
                           value="1" min="1" max="30" 
                           class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-teal-500 focus:border-teal-500" required>
                    <span class="text-[11px] text-slate-500">Tambahan ini otomatis menambah jatah &amp; sisa cuti pegawai.</span>
                </div>

                <div>
                    <label for="keterangan" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Alasan / Keterangan Cuti Khusus <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="keterangan" id="keterangan" 
                           placeholder="Contoh: Bonus cuti prestasi kantor / Lembur proyek" 
                           class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-teal-500 focus:border-teal-500" required>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCutiKhususModal()" class="px-4 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow">
                    + Tambahkan Kuota Cuti
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openResetModal() {
        document.getElementById('resetModal').classList.remove('hidden');
        document.getElementById('resetModal').classList.add('flex');
    }

    function closeResetModal() {
        document.getElementById('resetModal').classList.add('hidden');
        document.getElementById('resetModal').classList.remove('flex');
    }

    function openCutiKhususModal(id, nama) {
        document.getElementById('modalNamaPegawai').innerText = nama;
        document.getElementById('cutiKhususForm').action = '/pegawai/' + id + '/tambah-cuti-khusus';
        document.getElementById('cutiKhususModal').classList.remove('hidden');
        document.getElementById('cutiKhususModal').classList.add('flex');
    }

    function closeCutiKhususModal() {
        document.getElementById('cutiKhususModal').classList.add('hidden');
        document.getElementById('cutiKhususModal').classList.remove('flex');
    }
</script>
@endpush
