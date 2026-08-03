<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with(['divisi'])->orderBy('nama', 'asc');

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        $pegawais = $query->paginate(15)->withQueryString();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();

        return view('pegawai.index', compact('pegawais', 'divisis'));
    }

    public function create()
    {
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();
        return view('pegawai.create', compact('divisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:30|unique:pegawais,nip',
            'nama' => 'required|string|max:100',
            'divisi_id' => 'required|exists:divisis,id',
            'jabatan' => 'required|string|max:100',
            'email' => 'required|email|unique:pegawais,email',
            'no_hp' => 'required|string|max:20',
            'jatah_cuti' => 'required|integer|min:0',
        ], [
            'nip.required' => 'NIP pegawai wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'divisi_id.required' => 'Divisi/Prodi wajib dipilih.',
            'email.unique' => 'Email pegawai sudah terdaftar.',
        ]);

        Pegawai::create([
            'nip' => trim($request->nip),
            'nama' => trim($request->nama),
            'divisi_id' => $request->divisi_id,
            'jabatan' => trim($request->jabatan),
            'email' => strtolower(trim($request->email)),
            'no_hp' => trim($request->no_hp),
            'jatah_cuti' => $request->jatah_cuti,
            'sisa_cuti' => $request->jatah_cuti,
        ]);

        return redirect()->route('pegawai.index')
            ->with('success', "Pegawai {$request->nama} berhasil ditambahkan!");
    }

    public function edit(Pegawai $pegawai)
    {
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();
        return view('pegawai.edit', compact('pegawai', 'divisis'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nip' => 'required|string|max:30|unique:pegawais,nip,' . $pegawai->id,
            'nama' => 'required|string|max:100',
            'divisi_id' => 'required|exists:divisis,id',
            'jabatan' => 'required|string|max:100',
            'email' => 'required|email|unique:pegawais,email,' . $pegawai->id,
            'no_hp' => 'required|string|max:20',
            'jatah_cuti' => 'required|integer|min:0',
            'sisa_cuti' => 'required|integer|min:0',
        ]);

        $pegawai->update([
            'nip' => trim($request->nip),
            'nama' => trim($request->nama),
            'divisi_id' => $request->divisi_id,
            'jabatan' => trim($request->jabatan),
            'email' => strtolower(trim($request->email)),
            'no_hp' => trim($request->no_hp),
            'jatah_cuti' => $request->jatah_cuti,
            'sisa_cuti' => $request->sisa_cuti,
        ]);

        return redirect()->route('pegawai.index')
            ->with('success', "Data pegawai {$pegawai->nama} berhasil diperbarui!");
    }

    public function destroy(Pegawai $pegawai)
    {
        $nama = $pegawai->nama;
        $pegawai->delete();

        return redirect()->route('pegawai.index')
            ->with('success', "Pegawai {$nama} berhasil dihapus.");
    }
}
