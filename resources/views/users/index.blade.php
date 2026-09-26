@extends('layouts.app')

@section('title', 'Kelola Akun User Login')

@section('content')
<div class="space-y-8 text-slate-900">
    
    <!-- Header Banner & Add Button -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 p-6 sm:p-10 text-white shadow-2xl border border-slate-800/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/15 text-indigo-300 rounded-full border border-indigo-500/30 text-[11px] font-bold uppercase tracking-wider mb-3 backdrop-blur-sm">
                <i data-lucide="user-cog" class="w-3.5 h-3.5 text-indigo-400"></i>
                <span>Akses &amp; Hak Pengguna</span>
            </div>
            <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                Manajemen Akun Pengguna Pejabat
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                Kelola akun pejabat (Kepala Divisi/Kaprodi, Tim HRD, dan Ketua STIKes) yang memiliki hak akses persetujuan multi-level.
            </p>
        </div>

        <a href="{{ route('users.create') }}" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-display font-bold text-xs shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2 shrink-0 relative z-10">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>+ Akun Pejabat Baru</span>
        </a>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-200/90">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari Nama Pengguna atau Email..." 
                       class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600">
            </div>

            <div>
                <select name="role" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-300 p-3 bg-slate-50 text-slate-900 text-xs font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 cursor-pointer">
                    <option value="">-- Semua Role Akses --</option>
                    <option value="hrd" {{ request('role') == 'hrd' ? 'selected' : '' }}>Tim HRD &amp; Kepegawaian (Admin)</option>
                    <option value="kadiv" {{ request('role') == 'kadiv' ? 'selected' : '' }}>Kepala Divisi / Kaprodi (Kadiv)</option>
                    <option value="ketua" {{ request('role') == 'ketua' ? 'selected' : '' }}>Ketua STIKes</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-display font-bold rounded-2xl text-xs flex items-center gap-1.5 shadow transition-all">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari User</span>
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('users.index') }}" class="px-5 py-3 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold rounded-2xl text-xs transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-display font-bold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-4">Nama Akun &amp; Posisi</th>
                        <th class="p-4">Email Login</th>
                        <th class="p-4">Role Akses</th>
                        <th class="p-4">Divisi / Prodi Terkait</th>
                        <th class="p-4 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4">
                                <div class="font-display font-bold text-slate-900 text-sm">{{ $user->name }}</div>
                                @if($user->id === Auth::id())
                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md font-bold border border-emerald-200 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Sesi Anda Saat Ini
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-slate-800 font-bold">
                                {{ $user->email }}
                            </td>
                            <td class="p-4">
                                @if($user->isHrd())
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 font-bold rounded-full text-[11px] border border-indigo-200 inline-block">
                                        HRD &bull; Admin
                                    </span>
                                @elseif($user->isKadiv())
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 font-bold rounded-full text-[11px] border border-blue-200 inline-block">
                                        Kepala Divisi
                                    </span>
                                @elseif($user->isKetua())
                                    <span class="px-3 py-1 bg-amber-100 text-amber-800 font-bold rounded-full text-[11px] border border-amber-200 inline-block">
                                        Ketua STIKes
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-800 font-bold rounded-full text-[11px] inline-block">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($user->divisi)
                                    <span class="font-semibold text-teal-900 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200">
                                        {{ $user->divisi->nama_divisi }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">- (Seluruh Institusi)</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Reset Password Button -->
                                    <button type="button" onclick="openResetPasswordModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')" 
                                            class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-xl text-xs border border-amber-200 flex items-center gap-1.5 transition-colors" title="Reset Password Akun">
                                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                        <span>Reset Password</span>
                                    </button>

                                    <!-- Edit User -->
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl font-semibold text-xs border border-blue-200 transition-colors" title="Edit Akun">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>

                                    <!-- Delete User -->
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }} ({{ $user->email }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl font-semibold text-xs border border-rose-200 transition-colors" title="Hapus Akun">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-slate-500">
                                <i data-lucide="users" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-display font-bold text-sm text-slate-700">Belum Ada Data Akun Pengguna</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="resetPasswordModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 border border-slate-200 text-slate-900 animate-in fade-in zoom-in-95 duration-200">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mb-4">
            <i data-lucide="key-round" class="w-6 h-6"></i>
        </div>
        <h3 class="font-display text-lg font-bold text-slate-900 mb-1">
            Reset Password Akun
        </h3>
        <p class="text-xs text-slate-500 mb-5">Akun: <span id="resetModalUserName" class="font-bold text-slate-900"></span> (<span id="resetModalUserEmail" class="font-mono text-slate-700"></span>)</p>

        <form id="resetPasswordForm" method="POST" action="">
            @csrf
            <div class="space-y-4 mb-5">
                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                        Password Baru <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="new_password" id="new_password" 
                           placeholder="Minimal 6 karakter..." 
                           class="w-full rounded-2xl border-slate-300 p-3.5 bg-slate-50 text-slate-900 text-xs sm:text-sm font-semibold focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500" required minlength="6">
                </div>
            </div>

            <div class="flex justify-end gap-2.5">
                <button type="button" onclick="closeResetPasswordModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold rounded-xl text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-display font-bold rounded-xl text-xs shadow-lg shadow-amber-600/30 transition-all">
                    Update Password
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
