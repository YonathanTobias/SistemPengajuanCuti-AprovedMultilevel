<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    private function authorizeHrd()
    {
        if (!Auth::user() || !Auth::user()->isHrd()) {
            abort(403, 'Akses Ditolak: Fitur Kelola Data Pegawai hanya diperuntukkan bagi HRD.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeHrd();

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
        $this->authorizeHrd();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();
        return view('pegawai.create', compact('divisis'));
    }

    public function store(Request $request)
    {
        $this->authorizeHrd();

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
        $this->authorizeHrd();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();
        return view('pegawai.edit', compact('pegawai', 'divisis'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $this->authorizeHrd();

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
            ->with('success', "Data & sisa kuota cuti pegawai {$pegawai->nama} berhasil diperbarui!");
    }

    public function tambahCutiKhusus(Request $request, Pegawai $pegawai)
    {
        $this->authorizeHrd();

        $request->validate([
            'jumlah_tambahan' => 'required|integer|min:1|max:30',
            'keterangan' => 'required|string|min:3',
        ]);

        $tambahan = (int) $request->jumlah_tambahan;

        $pegawai->increment('jatah_cuti', $tambahan);
        $pegawai->increment('sisa_cuti', $tambahan);

        return redirect()->route('pegawai.index')
            ->with('success', "Berhasil menambahkan +{$tambahan} hari Cuti Khusus untuk {$pegawai->nama}!");
    }

    public function resetKuotaManual(Request $request)
    {
        $this->authorizeHrd();

        $newQuota = (int) $request->input('target_quota', 0);
        
        $count = Pegawai::query()->update([
            'sisa_cuti' => $newQuota,
        ]);

        return redirect()->route('pegawai.index')
            ->with('success', "Berhasil mereset sisa kuota cuti seluruh pegawai ({$count} pegawai) menjadi {$newQuota} hari!");
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->authorizeHrd();
        $nama = $pegawai->nama;
        $pegawai->delete();

        return redirect()->route('pegawai.index')
            ->with('success', "Pegawai {$nama} berhasil dihapus.");
    }
}
