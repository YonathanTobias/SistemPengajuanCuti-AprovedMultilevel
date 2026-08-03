<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::with(['kadivUser', 'pegawais'])->orderBy('nama_divisi', 'asc')->get();
        return view('divisi.index', compact('divisis'));
    }

    public function create()
    {
        return view('divisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:20|unique:divisis,kode_divisi',
            'nama_divisi' => 'required|string|max:100|unique:divisis,nama_divisi',
            'deskripsi' => 'nullable|string',
            'email_kadiv' => 'nullable|email|unique:users,email',
            'nama_kadiv' => 'nullable|string|max:100',
        ], [
            'kode_divisi.required' => 'Kode divisi wajib diisi.',
            'kode_divisi.unique' => 'Kode divisi sudah digunakan.',
            'nama_divisi.required' => 'Nama divisi/prodi wajib diisi.',
            'nama_divisi.unique' => 'Nama divisi/prodi sudah ada.',
            'email_kadiv.unique' => 'Email Kepala Divisi sudah terdaftar.',
        ]);

        $divisi = Divisi::create([
            'kode_divisi' => strtoupper(trim($request->kode_divisi)),
            'nama_divisi' => trim($request->nama_divisi),
            'deskripsi' => $request->deskripsi,
        ]);

        // AUTOMATIC KADIV ACCOUNT CREATION REQUIREMENT
        $slug = Str::slug($divisi->nama_divisi);
        $kadivEmail = $request->email_kadiv ?: "kadiv.{$slug}@stikespantiwaluya.ac.id";
        $kadivName = $request->nama_kadiv ?: "Kepala " . $divisi->nama_divisi;
        $defaultPassword = 'password123';

        // Ensure email uniqueness fallback
        if (User::where('email', $kadivEmail)->exists()) {
            $kadivEmail = "kadiv.{$slug}." . Str::lower(Str::random(3)) . "@stikespantiwaluya.ac.id";
        }

        $kadivUser = User::create([
            'name' => $kadivName,
            'email' => $kadivEmail,
            'password' => Hash::make($defaultPassword),
            'role' => 'kadiv',
            'divisi_id' => $divisi->id,
        ]);

        $credentialInfo = "Akun Kepala Divisi otomatis dibuat!\nEmail: {$kadivEmail}\nPassword: {$defaultPassword}";

        return redirect()->route('divisi.index')
            ->with('success', "Divisi/Prodi '{$divisi->nama_divisi}' berhasil ditambahkan! {$credentialInfo}");
    }

    public function edit(Divisi $divisi)
    {
        $divisi->load('kadivUser');
        return view('divisi.edit', compact('divisi'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:20|unique:divisis,kode_divisi,' . $divisi->id,
            'nama_divisi' => 'required|string|max:100|unique:divisis,nama_divisi,' . $divisi->id,
            'deskripsi' => 'nullable|string',
        ]);

        $divisi->update([
            'kode_divisi' => strtoupper(trim($request->kode_divisi)),
            'nama_divisi' => trim($request->nama_divisi),
            'deskripsi' => $request->deskripsi,
        ]);

        // Also update Kadiv user name if attached
        if ($divisi->kadivUser && $request->filled('nama_kadiv')) {
            $divisi->kadivUser->update([
                'name' => $request->nama_kadiv,
            ]);
        }

        return redirect()->route('divisi.index')
            ->with('success', "Data Divisi '{$divisi->nama_divisi}' berhasil diperbarui.");
    }

    public function destroy(Divisi $divisi)
    {
        $nama = $divisi->nama_divisi;
        
        // Delete related kadiv user if exists
        User::where('divisi_id', $divisi->id)->delete();
        
        $divisi->delete();

        return redirect()->route('divisi.index')
            ->with('success', "Divisi '{$nama}' dan akun Kepala Divisi terkait berhasil dihapus.");
    }
}
