@extends('layouts.app')

@section('title', 'Kelola Akun User Login')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Akun User Login</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola akun pejabat (Kepala Divisi, HRD, dan Ketua STIKes) yang memiliki akses login ke sistem</p>
        </div>

        <a href="{{ route('users.create') }}" class="px-5 py-3 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>+ Tambah Akun User Baru</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari Nama Pengguna atau Email..." 
                       class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
            </div>

            <div>
                <select name="role" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 p-2.5 bg-slate-50 text-slate-900 text-xs font-medium focus:ring-blue-500">
                    <option value="">-- Semua Role Akses --</option>
                    <option value="hrd" {{ request('role') == 'hrd' ? 'selected' : '' }}>Tim HRD &amp; Kepegawaian (Admin)</option>
                    <option value="kadiv" {{ request('role') == 'kadiv' ? 'selected' : '' }}>Kepala Divisi / Kaprodi (Kadiv)</option>
                    <option value="ketua" {{ request('role') == 'ketua' ? 'selected' : '' }}>Ketua STIKes</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center gap-1.5">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Cari</span>
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('users.index') }}" class="px-4 py-2.5 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
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
                        <th class="p-4">Nama Akun / Posisi</th>
                        <th class="p-4">Email Login</th>
                        <th class="p-4">Role Akses</th>
                        <th class="p-4">Divisi / Prodi Terkait</th>
                        <th class="p-4 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <span class="font-bold text-slate-900 text-sm block">{{ $user->name }}</span>
                                @if($user->id === Auth::id())
                                    <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-bold border border-emerald-200">Akun Anda Saat Ini</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-slate-800 font-bold">
                                {{ $user->email }}
                            </td>
                            <td class="p-4">
                                @if($user->isHrd())
                                    <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 font-bold rounded-full text-[11px] border border-indigo-200">
                                        HRD / Admin
                                    </span>
                                @elseif($user->isKadiv())
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 font-bold rounded-full text-[11px] border border-blue-200">
                                        Kepala Divisi
                                    </span>
                                @elseif($user->isKetua())
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold rounded-full text-[11px] border border-amber-200">
                                        Ketua STIKes
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-800 font-bold rounded-full text-[11px]">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($user->divisi)
                                    <span class="font-semibold text-teal-900 bg-teal-50 px-2 py-1 rounded border border-teal-200">
                                        {{ $user->divisi->nama_divisi }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">- (Seluruh Unit Instansi)</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Reset Password Button -->
                                    <button type="button" onclick="openResetPasswordModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')" 
                                            class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg text-xs border border-amber-200 flex items-center gap-1" title="Reset Password Akun">
                                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                        <span>Reset Password</span>
                                    </button>

                                    <!-- Edit User -->
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg font-semibold text-xs border border-blue-200 transition-colors" title="Edit Akun User">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>

                                    <!-- Delete User -->
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }} ({{ $user->email }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold text-xs border border-rose-200 transition-colors" title="Hapus Akun">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                <i data-lucide="users" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-semibold">Belum Ada Data Akun User</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="resetPasswordModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200">
        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-1 text-amber-700">
            <i data-lucide="key-round" class="w-5 h-5"></i>
            Reset Password User
        </h3>
        <p class="text-xs text-slate-600 mb-4">Akun: <span id="resetModalUserName" class="font-bold text-slate-900"></span> (<span id="resetModalUserEmail" class="font-mono text-slate-700"></span>)</p>

        <form id="resetPasswordForm" method="POST" action="">
            @csrf
            <div class="mb-5">
                <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Password Baru <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="new_password" id="new_password" value="password123" 
                       class="w-full rounded-xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-sm font-bold focus:ring-amber-500" required>
                <span class="text-[11px] text-slate-500 mt-1 block">Default: <code class="bg-slate-200 px-1 rounded">password123</code> (Bisa diubah sesuai keinginan)</span>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs shadow">
                    Konfirmasi Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openResetPasswordModal(id, name, email) {
        document.getElementById('resetModalUserName').innerText = name;
        document.getElementById('resetModalUserEmail').innerText = email;
        document.getElementById('resetPasswordForm').action = '/users/' + id + '/reset-password';
        document.getElementById('resetPasswordModal').classList.remove('hidden');
        document.getElementById('resetPasswordModal').classList.add('flex');
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
        document.getElementById('resetPasswordModal').classList.remove('flex');
    }
</script>
@endpush
