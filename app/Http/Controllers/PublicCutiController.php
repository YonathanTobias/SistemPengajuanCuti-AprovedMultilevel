<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicCutiController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::with('divisi')->orderBy('nama', 'asc')->get();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();

        return view('public.pengajuan', compact('pegawais', 'divisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_cuti' => 'required|string',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10',
            'alamat_cuti' => 'required|string|min:5',
            'no_hp_cuti' => 'required|string|min:8',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'pegawai_id.required' => 'Silakan pilih pegawai pengaju cuti.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai cuti minimal hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'alasan.min' => 'Alasan cuti harus lebih jelas (minimal 10 karakter).',
            'file_pendukung.max' => 'Ukuran file pendukung maksimal 2MB.',
        ]);

        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        $start = Carbon::parse($request->tanggal_mulai);
        $end = Carbon::parse($request->tanggal_selesai);
        $jumlahHari = $start->diffInDays($end) + 1;

        // Check annual leave quota
        if ($request->jenis_cuti === 'Cuti Tahunan') {
            if ($pegawai->sisa_cuti < $jumlahHari) {
                return back()->withInput()->with('error', "Sisa cuti tahunan saudara {$pegawai->nama} hanya {$pegawai->sisa_cuti} hari, tidak mencukupi untuk pengajuan {$jumlahHari} hari.");
            }
        }

        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');
            $filename = time() . '_' . Str::slug($pegawai->nama) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/berkas'), $filename);
            $filePath = 'uploads/berkas/' . $filename;
        }

        // Generate unique tracking code
        $kodeTracking = 'CUTI-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $cuti = Cuti::create([
            'kode_tracking' => $kodeTracking,
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_hari' => $jumlahHari,
            'alasan' => $request->alasan,
            'alamat_cuti' => $request->alamat_cuti,
            'no_hp_cuti' => $request->no_hp_cuti,
            'file_pendukung' => $filePath,
            'status' => 'pending_kadiv',
        ]);

        return redirect()->route('public.tracking', ['kode' => $kodeTracking])
            ->with('success', "Pengajuan cuti berhasil dikirim! Simpan Kode Tracking Anda: {$kodeTracking}");
    }

    public function tracking(Request $request)
    {
        $search = $request->input('kode');
        $cuti = null;
        $cutiList = collect();

        if ($search) {
            $searchClean = trim($search);
            // Search by exact tracking code or NIP
            $cuti = Cuti::with(['pegawai.divisi'])
                ->where('kode_tracking', $searchClean)
                ->first();

            if (!$cuti) {
                // Search list by Pegawai NIP or Nama
                $cutiList = Cuti::with(['pegawai.divisi'])
                    ->whereHas('pegawai', function ($q) use ($searchClean) {
                        $q->where('nip', $searchClean)
                          ->orWhere('nama', 'like', "%{$searchClean}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('public.tracking', compact('cuti', 'cutiList', 'search'));
    }

    public function suratCuti($kode_tracking)
    {
        $cuti = Cuti::with(['pegawai.divisi'])
            ->where('kode_tracking', $kode_tracking)
            ->where('status', 'approved')
            ->firstOrFail();

        return view('public.surat', compact('cuti'));
    }
}
