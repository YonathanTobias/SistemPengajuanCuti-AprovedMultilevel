<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function authorizeHrd()
    {
        if (!Auth::user() || !Auth::user()->isHrd()) {
            abort(403, 'Akses Ditolak: Fitur Kelola User hanya diperuntukkan bagi HRD.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeHrd();

        $query = User::with('divisi')->orderBy('role', 'asc')->orderBy('name', 'asc');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeHrd();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();
        return view('users.create', compact('divisis'));
    }

    public function store(Request $request)
    {
        $this->authorizeHrd();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:hrd,kadiv,ketua',
            'divisi_id' => 'nullable|required_if:role,kadiv|exists:divisis,id',
        ], [
            'name.required' => 'Nama pengguna/posisi wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar untuk pengguna lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'divisi_id.required_if' => 'Untuk role Kepala Divisi, Divisi/Prodi wajib dipilih.',
        ]);

        User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'divisi_id' => $request->role === 'kadiv' ? $request->divisi_id : null,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna '{$request->name}' ({$request->email}) berhasil dibuat!");
    }

    public function edit(User $user)
    {
        $this->authorizeHrd();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();
        return view('users.edit', compact('user', 'divisis'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeHrd();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:hrd,kadiv,ketua',
            'divisi_id' => 'nullable|required_if:role,kadiv|exists:divisis,id',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'role' => $request->role,
            'divisi_id' => $request->role === 'kadiv' ? $request->divisi_id : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna '{$user->name}' berhasil diperbarui!");
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->authorizeHrd();

        $newPassword = $request->input('new_password', 'password123');
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Password untuk akun '{$user->name}' ({$user->email}) berhasil direset menjadi: {$newPassword}");
    }

    public function destroy(User $user)
    {
        $this->authorizeHrd();

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan untuk login.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna '{$nama}' berhasil dihapus.");
    }
}
